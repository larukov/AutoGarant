document.addEventListener("DOMContentLoaded", function () {
    // Получаем цену машины из Blade переменной через дата-атрибут или напрямую
    const carPrice = parseFloat("{{ $car->price }}");

    // Элементы управления (слайдеры)
    const downPaymentRange = document.getElementById("downPaymentRange");
    const periodRange = document.getElementById("periodRange");

    // Текстовые поля вывода значений
    const downPaymentValue = document.getElementById("downPaymentValue");
    const periodValue = document.getElementById("periodValue");
    const monthlyPaymentValue = document.getElementById("monthlyPaymentValue");
    const interestRateInput = document.getElementById("interestRate");

    // Функция форматирования чисел в красивую валюту (например: 1 500 000 ₽)
    function formatCurrency(value) {
        return new Intl.NumberFormat("ru-RU", {
            style: "currency",
            currency: "RUB",
            maximumFractionDigits: 0,
        }).format(value);
    }

    // Функция ежемесячного аннуитетного расчета
    function calculateLoan() {
        const downPayment = parseFloat(downPaymentRange.value);
        const months = parseInt(periodRange.value);
        const annualRate = parseFloat(interestRateInput.value);

        // Обновляем текст под слайдерами
        downPaymentValue.innerText = formatCurrency(downPayment);
        periodValue.innerText =
            months + " мес. (" + Math.round(months / 12) + " г.)";

        // Тело кредита = Стоимость машины - Первоначальный взнос
        const loanAmount = carPrice - downPayment;

        if (loanAmount <= 0) {
            monthlyPaymentValue.innerText = "0 ₽";
            return;
        }

        // Ежемесячная процентная ставка (годовая ставка / 12 месяцев / 100)
        const monthlyRate = annualRate / 12 / 100;

        const coefficient =
            (monthlyRate * Math.pow(1 + monthlyRate, months)) /
            (Math.pow(1 + monthlyRate, months) - 1);
        const monthlyPayment = loanAmount * coefficient;

        // Выводим результат
        monthlyPaymentValue.innerText = formatCurrency(monthlyPayment);
    }

    // Вешаем слушатели событий на ползунки для динамического пересчета при движении
    downPaymentRange.addEventListener("input", calculateLoan);
    periodRange.addEventListener("input", calculateLoan);

    // Первичный расчет при загрузке страницы
    calculateLoan();
});
