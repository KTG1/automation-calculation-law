# Settlement Calculator WordPress Plugin

An installable WordPress plugin that creates a published `/settlement-calculator/` page on activation and renders a responsive personal-injury settlement estimator with tabbed FAQs.

## Installation

1. Download `settlement-calculator.zip` from the project root.
2. In WordPress, go to **Plugins → Add New → Upload Plugin**.
3. Upload the ZIP and activate the plugin.
4. Open `/settlement-calculator/` on your site.

You can also render the calculator on another page with:

```text
[settlement_calculator]
```

## Customization

Open **Settings → Settlement Calculator** in WordPress to edit every public heading, label, helper line, result term, and disclaimer. The same screen lets administrators add or remove FAQ tabs, questions, and answers.

Version 1.3.0 also adds editable branding, navigation labels, a three-stage value map, icon-led claim-factor explanations, read-more arrows, a worked example, a privacy callout, and configurable consultation CTAs.

## Calculation model

- Economic damages are the sum of medical, income, property, and other losses.
- Non-economic damages equal medical expenses (past and future) multiplied by the selected pain-and-suffering factor.
- Comparative fault reduces the estimated gross claim.
- Attorney fees, case costs, and liens are deducted to estimate take-home value.
- The displayed likely range is ±15% of the take-home estimate.

This is a general planning tool, not legal advice or a prediction of a case outcome.
