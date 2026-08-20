# Settlement Calculator WordPress Plugin

An installable WordPress plugin that creates a published `/settlement-calculator/` page on activation and renders a responsive personal-injury settlement estimator.

## Installation

1. Download `settlement-calculator.zip` from the project root.
2. In WordPress, go to **Plugins → Add New → Upload Plugin**.
3. Upload the ZIP and activate the plugin.
4. Open `/settlement-calculator/` on your site.

You can also render the calculator on another page with:

```text
[settlement_calculator]
```

## Calculation model

- Economic damages are the sum of medical, income, property, and other losses.
- Non-economic damages equal medical expenses (past and future) multiplied by the selected pain-and-suffering factor.
- Comparative fault reduces the estimated gross claim.
- Attorney fees, case costs, and liens are deducted to estimate take-home value.
- The displayed likely range is ±15% of the take-home estimate.

This is a general planning tool, not legal advice or a prediction of a case outcome.
