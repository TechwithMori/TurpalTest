<?php

namespace App\Services\V1;

use App\Models\Experience;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Availability;

class ExperienceService
{
    protected $cacheDuration = 3600;

    public function __construct()
    {
        $this->cacheDuration = env('EXPERIENCE_CACHE_DURATION', 3600);
    }

    /**
     * @param \Carbon\Carbon $startDate
     * @param \Carbon\Carbon $endDate
     * @return array
     */
    public function getAvailableExperiences($startDate, $endDate)
    {
        $cacheKey = 'experiences_' . $startDate->format('Y-m-d') . '_' . $endDate->format('Y-m-d');

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($startDate, $endDate) {
            $experiences = Experience::whereHas('availabilities', function ($query) use ($startDate, $endDate) {
                $query->whereNot(fn ($query) => $query->where('start_time', '>', $endDate)
                    ->orWhere('end_time', '<', $startDate));
            })
            ->when(request('category_id'), function ($query) {
                $query->where('category_id', request('category_id'));
            })
            ->when(request('tag_id'), function ($query) {
                $query->whereHas('tags', function ($query) {
                    $query->where('tags.id', request('tag_id'));
                });
            })
            ->get();

            return $experiences->map(function ($experience) use ($startDate, $endDate) {
                $availabilities = $experience->availabilities
                    ->whereNot(fn ($query) => $query->where('start_time', '>', $endDate)
                    ->orWhere('end_time', '<', $startDate));

                return [
                    'id' => $experience->id,
                    'slug' => $experience->slug,
                    'title' => $experience->title,
                    'thumbnail' => $experience->thumbnail ?? 'https://picsum.photos/300/200',
                    'short_description' => $experience->short_description,
                    'sell_price' => number_format($availabilities->min('sell_price'), 2) . ' USD',
                    'buy_price' => number_format($availabilities->min('buy_price'), 2) . ' USD',
                    'rating' => $experience->rating,
                    'city' => $experience->city_id,
                    'country_code' => $experience->country_code,
                    'language' => $experience->language,
                    'latitude' => $experience->latitude,
                    'longitude' => $experience->longitude,
                ];
            })->toArray();
        });
    }

    public function updateViews($experienceId)
    {
        return DB::table('experiences')
            ->where('id', $experienceId)
            ->increment('views');
    }

    public function purchase($validatedData)
    {
        try {
            DB::beginTransaction();
            $experienceId = $validatedData['experience_id'];
            $details = json_decode($validatedData['details'], true);
            $selectedDate = $validatedData['selected_date'];


            $experience = Experience::find($experienceId);
            if (!$experience) {
                throw new Exception('Experience not found!');
            }

            $vat = config('travello.country_vat')[$experience->country_code] ?? null;

            $invoice = Invoice::create([
                'experience_id' => $experienceId,
                'status' => 'pending',
                'channel' => 'online',
                'date' => now(),
                'buyer_name' => $validatedData['buyer_name'],
                'buyer_email' => $validatedData['buyer_email'],
                'buyer_phone' => $validatedData['buyer_phone'],
                'buyer_address' => $validatedData['buyer_address'],
                'buyer_city' => $validatedData['buyer_city'],
                'buyer_country' => $validatedData['buyer_country'],
            ]);


            $vatTotal = 0;
            $totalPax = 0;
            foreach ($details as $detail) {
                $availability = Availability::where('id', $detail['id'])
                    ->where('from', '<=', $selectedDate)
                    ->where('to', '>=', $selectedDate)
                    ->where('experience_id', $experienceId)
                    ->first();
                if (!$availability) {
                    throw new Exception('Availability not found!');
                }


                if ($experience->vat_not_included) {
                    $vatAmount = ($vat * $detail['count'] * $availability->sell_price / 100);
                } else {
                    $vatAmount = 0;
                }
                $vatTotal += $vatAmount;

                $totalPax += $detail['count'];
                $buyPrice = $detail['count'] * $availability->buy_price;
                $sellPrice = $detail['count'] * $availability->sell_price;


                $invoiceItem = InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'type' => 'EXPERIENCE',
                    'experience_id' => $experienceId,
                    'availability_id' => $availability->id,
                    'pax' => $detail['count'],
                    'execute_date' => $selectedDate,
                    'buy_price' => $buyPrice,
                    'sell_price' => $sellPrice,
                    'vat_amount' => $vatAmount,
                    'start' => $availability->start_time,
                    'end' => $availability->end_time,
                    'status' => 'pending',
                ]);

                $invoice->sum += $invoiceItem->sell_price;
                $invoiceItem->save();
            }

            $vatInvoiceItem = InvoiceItem::where('invoice_id', $invoice->id)->where('type', 'VAT')->first();
            if (!$vatInvoiceItem) { // VAT record not exists and should be created
                $vatInvoiceItem = InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'type' => 'VAT',
                    'experience_id' => $experienceId,
                    'availability_id' => 0,
                    'pax' => 0,
                    'execute_date' => null,
                    'buy_price' => 0,
                    'sell_price' => $vatTotal,
                    'vat_amount' => 0,
                    'start' => now(),
                    'end' => now(),
                    'status' => 'pending',
                ]);
            } else { // VAT record exists and should be updated
                $vatInvoiceItem->sell_price += $vatTotal;
                $vatInvoiceItem->save();
            }

            $invoice->sum += $vatTotal;

            $invoice->save();

            $result = [
                'status' => true,
                'data' => [
                    'invoice_id' => $invoice->id
                ]
            ];

            // sending email
            $selectedDate = new \DateTime($selectedDate);

            $emailData = [
                1 => $invoice->id . ' - ' . $selectedDate->format('Y-m-d') . ' - ' . $selectedDate->format('D') . ' - *pending*',
                2 => $experience->title,
                3 => $experience->id,
                3 => 'Pax no: ' . $totalPax,
            ];

            $emailContent = __('notification_email_invoice_body', [], 'en');

            foreach ($emailData as $key => $val) {
                $emailContent = str_replace('{{' . $key . '}}', $val, $emailContent);
            }

            Mail::raw($emailContent, function ($message) use ($emailData) {
                $message->to(config('travello.notification.email'))
                    ->subject('Invoice ' . $emailData[1] . ' pending');
            });

            DB::commit();

            return $result;
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage() . " " . $e->getFile() . " " . $e->getLine()
            ];
        }
    }
}
