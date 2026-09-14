const currencyFormatter = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

const dateFormatter = new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
});

const dateTimeFormatter = new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
});

export function formatCurrency(value: number | null | undefined): string {
    return currencyFormatter.format(value ?? 0);
}

export function formatDate(value: string | null | undefined): string {
    if (!value) {
        return '-';
    }

    return dateFormatter.format(new Date(value));
}

export function formatDateTime(value: string | null | undefined): string {
    if (!value) {
        return '-';
    }

    return dateTimeFormatter.format(new Date(value));
}

export function formatPercent(value: number | null | undefined): string {
    if (value === null || value === undefined) {
        return '-';
    }

    return `${value}%`;
}
