(function () {
  'use strict';

  const forms = document.querySelectorAll('[data-sc-form]');

  const numberValue = (form, name) => {
    const field = form.elements.namedItem(name);
    const value = field ? Number.parseFloat(field.value) : 0;
    return Number.isFinite(value) ? Math.max(0, value) : 0;
  };

  const money = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 0
  });

  forms.forEach((form) => {
    const shell = form.closest('.sc-shell');
    if (!shell) return;

    const output = (selector, value) => {
      const element = shell.querySelector(selector);
      if (element) element.textContent = value;
    };

    const calculate = () => {
      const medical = numberValue(form, 'medical');
      const futureMedical = numberValue(form, 'future-medical');
      const economic = medical + futureMedical + numberValue(form, 'lost-income') +
        numberValue(form, 'future-income') + numberValue(form, 'property') + numberValue(form, 'other');
      const multiplier = Math.min(5, Math.max(1, numberValue(form, 'multiplier') || 1));
      const nonEconomic = (medical + futureMedical) * multiplier;
      const gross = economic + nonEconomic;
      const faultRate = Math.min(100, numberValue(form, 'fault')) / 100;
      const afterFault = gross * (1 - faultRate);
      const faultReduction = gross - afterFault;
      const feeAmount = afterFault * (Math.min(100, numberValue(form, 'fee')) / 100);
      const deductions = numberValue(form, 'case-costs') + numberValue(form, 'liens');
      const net = Math.max(0, afterFault - feeAmount - deductions);
      const low = Math.max(0, net * 0.85);
      const high = net * 1.15;

      output('[data-sc-economic]', money.format(economic));
      output('[data-sc-non-economic]', money.format(nonEconomic));
      output('[data-sc-gross]', money.format(gross));
      output('[data-sc-fault]', '−' + money.format(faultReduction));
      output('[data-sc-fee]', '−' + money.format(feeAmount));
      output('[data-sc-deductions]', '−' + money.format(deductions));
      output('[data-sc-net]', money.format(net));
      output('[data-sc-range]', money.format(low) + ' – ' + money.format(high) + ' likely range');
      output('[data-sc-multiplier-output]', multiplier.toFixed(1) + '×');
    };

    form.addEventListener('input', calculate);
    form.addEventListener('reset', () => window.setTimeout(calculate, 0));
    calculate();
  });
}());
