# Turpal - Backend Developer Technical Challenae

## 📖 Context

Welcome to your technical take-home challenge!

Imagine you're joining a travel company called **Travello** as a backend developer. The platform originally sold curated experiences (tours, events, etc.) directly. As the company has grown, it now faces increased load and the need to integrate with multiple third-party providers. It also needs to refactor parts of its codebase to support future scalability.

You're inheriting a **legacy Laravel codebase**. It's messy in parts, shows signs of tight coupling and missing abstractions, and has some performance issues — the typical realities of a fast-moving startup.

---

## 🚀 Challenge Scope

### 🔌 Integrate One Third-Party Provider

The idea is to implement a clean and extensible integration with one of the fictional third-party providers listed below, with future integrations in mind.

By **"transparent,"** we mean that the consumer of our internal API (e.g., the e-commerce website) should receive consistent, normalized data, regardless of whether it comes from our local database or a third-party provider. The source of the data should be abstracted away from the consumer.

Since this API currently powers a live e-commerce website, it's important to preserve the existing API schema and behavior. If any changes are necessary, be explicitly aware of their impact on the consuming client.

You are asked to integrate one of these providers:

- [Heavenly Tours API Docs](https://mock.turpal.com/docs/heavenly)
- [Majesty Travels API Docs](https://mock.turpal.com/docs/majesty)

**Considerations:**

- Ensure integration is clean and **transparent**
- Normalize provider responses into a **unified internal format**
- Design for **extensibility** to support future provider integrations without major rewrites

---

### 📡 Topics for the Follow-Up Interview

The following topics are outside the scope of the take-home assignment, but we may explore them during the follow-up discussion to better understand your thinking and approach:

- Challenges you encountered during the provider integration
- Refactoring strategies and performance considerations
- Ideas for improving the internal API design
- Designing an online booking flow
- Adapting the system for future needs such as:
  - E-commerce capabilities
  - Multi-tenancy
  - Localization and multi-language support
  - Multi-currency handling

---

## ✅ Delivery Instructions

1. Write us your best code.
2. Send us the link to your repository.
3. Add any extra notes or responses in `NOTES.md`.
4. Be prepared to discuss your code, decisions, and design in a follow-up interview.
5. Use of any AI tools is encouraged. You may be asked to use it in the follow-up interview.

---

We appreciate the time and thought you put into this. Good luck, and we look forward to reviewing your work!
