import React from "react";

// adding currency to amount.
function CurrencyFormatter({
    amount,
    currency = "PHP",
    locale,
}: {
    amount: number;
    currency?: string;
    locale?: string;
}) {
    return new Intl.NumberFormat(locale, {
        style: "currency",
        currency,
    }).format(amount);
}

export default CurrencyFormatter;
