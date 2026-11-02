document.addEventListener("DOMContentLoaded", function () {
    // Получаем цену машины из data-атрибута контейнера (передаётся из Blade)
    const calculator = document.getElementById("creditCalculator");
    if (!calculator) return;

    const carPrice = parseFloat(calculator.dataset.carPrice) || 0;

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
        const downPayment = parseFloat(downPaymentRange.value) || 0;
        const months = parseInt(periodRange.value) || 0;
        const annualRate = parseFloat(interestRateInput.value) || 0;

        downPaymentValue.innerText = formatCurrency(downPayment);
        periodValue.innerText = months + " мес. (" + Math.round(months / 12) + " г.)";

        const loanAmount = carPrice - downPayment;

        if (loanAmount <= 0 || months <= 0) {
            monthlyPaymentValue.innerText = "0 ₽";
            return;
        }

        const monthlyRate = annualRate / 12 / 100;
        const coefficient =
            (monthlyRate * Math.pow(1 + monthlyRate, months)) /
            (Math.pow(1 + monthlyRate, months) - 1);
        const monthlyPayment = loanAmount * coefficient;

        monthlyPaymentValue.innerText = formatCurrency(monthlyPayment);

        // Синхронизируем расчётные данные кредита с формой бронирования
        const orderDownPayment = document.getElementById("orderDownPayment");
        const orderLoanTerm = document.getElementById("orderLoanTerm");
        const orderInterestRate = document.getElementById("orderInterestRate");
        if (orderDownPayment) orderDownPayment.value = downPayment;
        if (orderLoanTerm) orderLoanTerm.value = months;
        if (orderInterestRate) orderInterestRate.value = annualRate;
    }

    downPaymentRange.addEventListener("input", calculateLoan);
    periodRange.addEventListener("input", calculateLoan);

    calculateLoan();
});