window.creditCalculator = {
    get price() {
        const c = document.getElementById('creditCalculator');
        return c ? parseFloat(c.dataset.carPrice) || 0 : 0;
    },
    set price(v) {
        const c = document.getElementById('creditCalculator');
        if (c) c.dataset.carPrice = v;
        const range = document.getElementById('downPaymentRange');
        if (range) range.max = v;
        calculateLoan();
    }
};

document.addEventListener("DOMContentLoaded", function () {
    const calculator = document.getElementById("creditCalculator");
    if (!calculator) return;

    const downPaymentRange = document.getElementById("downPaymentRange");
    const periodRange = document.getElementById("periodRange");

    const downPaymentValue = document.getElementById("downPaymentValue");
    const periodValue = document.getElementById("periodValue");
    const monthlyPaymentValue = document.getElementById("monthlyPaymentValue");
    const interestRateInput = document.getElementById("interestRate");

    function formatCurrency(value) {
        return new Intl.NumberFormat("ru-RU", {
            style: "currency",
            currency: "RUB",
            maximumFractionDigits: 0,
        }).format(value);
    }

    function calculateLoan() {
        const carPrice = creditCalculator.price;
        const downPayment = parseFloat(downPaymentRange.value) || 0;
        const months = parseInt(periodRange.value) || 0;
        const annualRate = parseFloat(interestRateInput.value) || 0;

        downPaymentValue.innerText = formatCurrency(downPayment);
        periodValue.innerText = months + " мес. (" + Math.round(months / 12) + " г.)";

        const loanAmount = carPrice - downPayment;

        if (loanAmount <= 0 || months <= 0) {
            monthlyPaymentValue.innerText = "0 ₽";
        } else {
            const monthlyRate = annualRate / 12 / 100;
            const coefficient =
                (monthlyRate * Math.pow(1 + monthlyRate, months)) /
                (Math.pow(1 + monthlyRate, months) - 1);
            const monthlyPayment = loanAmount * coefficient;
            monthlyPaymentValue.innerText = formatCurrency(monthlyPayment);
        }
    }

    // Делаем функцию глобально доступной для пересчёта при изменении опций/цены
    window.calculateLoan = calculateLoan;

    downPaymentRange.addEventListener("input", calculateLoan);
    periodRange.addEventListener("input", calculateLoan);

    calculateLoan();
});