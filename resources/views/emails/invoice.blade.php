<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                Invoice details
                            </td>

                            <td>
                                Invoice #: {{ $transaction->id }}<br />
                                Sent at:
                                {{ \Carbon\Carbon::parse($transaction->created_at)->format('M d, Y H:i:s') }}<br />
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <b>From:</b><br />
                                {{ $transaction->from->name }}<br />
                                {{ $transaction->from->email }}
                            </td>

                            <td>
                                <b>To:</b><br />
                                {{ $transaction->to->name }}<br />
                                {{ $transaction->to->email }}<br />
                                @if ($transaction->to->isType(\App\Models\Shopkeeper::class))
                                    {{ ucfirst($transaction->to->userable->document_type) . ': ' . $transaction->to->userable->document_number }}
                                @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Payment Method</td>

                <td></td>
            </tr>

            <tr class="details">
                <td>Transaction</td>

                <td></td>
            </tr>

            <tr class="heading">
                <td>Item</td>

                <td>Value</td>
            </tr>

            <tr class="item">
                <td>Transaction</td>

                <td>{{ Number::currency($transaction->amount) }}</td>
            </tr>

            <tr class="total">
                <td></td>

                <td>Total: {{ Number::currency($transaction->amount) }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
