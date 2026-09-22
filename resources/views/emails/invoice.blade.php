<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $emailJob->subject }}</title>
</head>

<body style="margin:0; padding:0; background-color:#f3f4f6; font-family:Arial, Helvetica, sans-serif; color:#111827;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f4f6; padding:30px 15px;">
    <tr>
        <td align="center">

            <table width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:600px; background:#ffffff; border:1px solid #e5e7eb;">

                <tr>
                    <td style="padding:24px 30px; border-bottom:1px solid #e5e7eb;">

                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>

                                <td valign="middle">
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <tr>
                                            <td valign="middle">
                                               @if ($logoUrl)
                                                    <img
                                                        src="{{ $logoUrl }}"
                                                        width="48"
                                                        height="48"
                                                        alt="{{ $companyName }}"
                                                        style="display:block; width:48px; height:48px; object-fit:contain; border:1px solid #e5e7eb;"
                                                    >
                                                @else
                                                    <table width="48" height="48" cellpadding="0" cellspacing="0" border="0" style="width:48px; height:48px; background:#111827;">
                                                        <tr>
                                                            <td align="center" valign="middle" style="font-size:16px; font-weight:bold; color:#ffffff;">
                                                                {{ $initials ?: 'CO' }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                @endif
                                            </td>

                                            <td valign="middle" style="padding-left:12px;">
                                                <div style="font-size:17px; font-weight:bold; color:#111827; line-height:1.3;">
                                                    {{ $companyName }}
                                                </div>

                                                <div style="font-size:11px; color:#6b7280; margin-top:3px;">
                                                    Electronic Invoicing
                                                </div>
                                            </td>

                                        </tr>
                                    </table>
                                </td>

                                <td align="right" valign="middle">
                                    <div style="font-size:10px; font-weight:bold; color:#6b7280; letter-spacing:0.5px;">
                                        INVOICE
                                    </div>

                                    <div style="font-size:10px; color:#9ca3af; margin-top:3px;">
                                        NOTIFICATION
                                    </div>
                                </td>

                            </tr>
                        </table>

                    </td>
                </tr>

                <tr>
                    <td style="padding:35px 30px;">

                        <p style="margin:0 0 20px; font-size:15px; color:#111827;">
                            Dear <strong>{{ $buyerName }}</strong>,
                        </p>

                        <p style="margin:0 0 18px; font-size:14px; line-height:1.7; color:#4b5563;">
                            We are pleased to inform you that your invoice document(s)
                            from <strong style="color:#111827;">{{ $companyName }}</strong>
                            are now available.
                        </p>

                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f9fafb; border:1px solid #e5e7eb;">
                            <tr>
                                <td style="padding:20px;">

                                    <div style="font-size:13px; font-weight:bold; color:#111827; margin-bottom:8px;">
                                        Invoice Documents
                                    </div>

                                    <div style="font-size:13px; line-height:1.6; color:#6b7280;">
                                        Please see the attached PDF invoice document(s)
                                        for your reference and records.
                                    </div>

                                    @if ($invoice)
                                        <div style="margin-top:12px; font-size:12px; color:#6b7280;">
                                            Invoice No:
                                            <strong style="color:#111827;">
                                                {{ $invoice->invoice_number }}
                                            </strong>
                                        </div>

                                        @if ($invoice->invoice_date)
                                            <div style="margin-top:4px; font-size:12px; color:#6b7280;">
                                                Invoice Date:
                                                <strong style="color:#111827;">
                                                    {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}
                                                </strong>
                                            </div>
                                        @endif
                                    @endif

                                </td>
                            </tr>
                        </table>

                        <p style="margin:22px 0 18px; font-size:14px; line-height:1.7; color:#4b5563;">
                            Kindly review the attached document(s). Should you have any
                            questions or require clarification regarding the invoice,
                            please contact our office.
                        </p>

                        <p style="margin:25px 0 0; font-size:14px; color:#4b5563;">
                            Thank you for your continued business.
                        </p>

                        <table cellpadding="0" cellspacing="0" border="0" style="margin-top:30px;">
                            <tr>
                                <td style="border-left:3px solid #111827; padding-left:14px;">

                                    <div style="font-size:14px; font-weight:bold; color:#111827;">
                                        {{ $companyName }}
                                    </div>

                                    <div style="font-size:12px; color:#6b7280; margin-top:4px;">
                                        Finance &amp; Accounts Receivable
                                    </div>

                                    <div style="font-size:12px; color:#6b7280; margin-top:2px;">
                                        Electronic Invoicing Department
                                    </div>

                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>

                <tr>
                    <td style="padding:22px 30px; background:#f9fafb; border-top:1px solid #e5e7eb;">

                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>

                                <td valign="top" style="width:60%;">

                                    <div style="font-size:12px; font-weight:bold; color:#374151;">
                                        {{ $companyName }}
                                    </div>

                                    @if ($seller?->tin)
                                        <div style="font-size:10px; color:#6b7280; margin-top:4px;">
                                            TIN: {{ $seller->tin }}
                                        </div>
                                    @endif

                                    @if ($seller?->address_line_1)
                                        <div style="font-size:10px; line-height:1.5; color:#6b7280; margin-top:4px;">
                                            {{ $seller->address_line_1 }}
                                        </div>
                                    @endif

                                    @if ($seller?->address_line_2)
                                        <div style="font-size:10px; line-height:1.5; color:#6b7280;">
                                            {{ $seller->address_line_2 }}
                                        </div>
                                    @endif

                                    <div style="font-size:10px; line-height:1.5; color:#6b7280;">

                                        @if ($seller?->barangay)
                                            Brgy. {{ $seller->barangay }},
                                        @endif

                                        @if ($seller?->city)
                                            {{ $seller->city }}
                                        @endif

                                        @if ($seller?->province)
                                            , {{ $seller->province }}
                                        @endif

                                        @if ($seller?->postal_code)
                                            {{ $seller->postal_code }}
                                        @endif

                                        @if ($seller?->country_code)
                                            , {{ $seller->country_code }}
                                        @endif

                                    </div>

                                </td>

                                <td align="right" valign="top" style="width:40%;">

                                    <div style="font-size:10px; color:#9ca3af;">
                                        Electronic Invoicing
                                    </div>

                                    <div style="font-size:10px; color:#9ca3af; margin-top:4px;">
                                        Finance &amp; Accounts Receivable
                                    </div>

                                </td>

                            </tr>
                        </table>

                        <div style="height:1px; background:#e5e7eb; margin:18px 0;"></div>

                        <p style="margin:0; font-size:10px; line-height:1.6; color:#9ca3af;">
                            This is an automated electronic invoice notification sent by
                            <strong>{{ $companyName }}</strong>.
                            Please do not reply to this automated message.
                        </p>

                        <p style="margin:8px 0 0; font-size:10px; line-height:1.6; color:#9ca3af;">
                            This email and its attachments may contain confidential
                            business information intended only for the recipient.
                        </p>

                    </td>
                </tr>

            </table>

            <p style="margin:15px 0 0; font-size:10px; color:#9ca3af; text-align:center;">
                Powered by E-Invoicing System
            </p>

        </td>
    </tr>
</table>

</body>
</html>
