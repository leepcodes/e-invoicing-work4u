<?php

namespace App\Services;

class InvoiceImportSchema
{
    public const COLUMNS = [
        'tin',
        'registered_name',
        'document_type',
        'invoice_date',
        'invoice_time',
        'due_date',
        'reference_number',
        'purchase_order_number',
        'currency_code',
        'accounting_currency_code',
        'exchange_rate',
        'exchange_rate_date',
        'exchange_rate_source',
        'amount_paid',
        'payment_terms',
        'payment_method',
        'payment_status',
        'system_branch_code',
        'fiscal_year',
        'source',
        'remarks',
        'item_code',
        'description',
        'quantity',
        'unit_code',
        'unit_price',
        'discount_amount',
        'tax_type',
        'tax_category',
        'tax_rate',
    ];

    public static function columns(): array
    {
        return self::COLUMNS;
    }

    public static function isColumn(string $column): bool
    {
        return in_array($column, self::COLUMNS, true);
    }

    public static function mapRow(array $row): array
    {
        $data = [];

        foreach (self::COLUMNS as $index => $column) {
            $data[$column] = $row[$index] ?? null;
        }

        return $data;
    }

    public static function hasHeaders(array $row): bool
    {
        $columns = array_map(
            fn ($value) => self::normalizeColumn($value),
            $row
        );

        return count(array_intersect(
            $columns,
            self::COLUMNS
        )) > 0;
    }

    public static function normalizeHeader(array $header): array
    {
        return array_map(
            fn ($value) => self::normalizeColumn($value),
            $header
        );
    }

    private static function normalizeColumn(mixed $value): string
    {
        $value = trim(
            (string) $value,
            " \t\n\r\0\x0B\xEF\xBB\xBF"
        );

        return strtolower(
            preg_replace('/[\s\-]+/', '_', $value)
        );
    }
}
