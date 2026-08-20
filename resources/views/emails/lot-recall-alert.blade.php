<x-mail::message>
# Important Recall Notice

Dear {{ $customer->name }},

We are contacting you regarding a medication you purchased from us. As part of our pharmacovigilance monitoring, we have identified a safety concern with the following batch.

**Medication:** {{ $medication->name }}
**Lot Number:** {{ $medication->lot_number }}
**Order Date:** {{ $order->purchase_date->toFormattedDateString() }}

## Recommended Action

Please stop using this medication immediately and contact our pharmacy as soon as possible to arrange a replacement or refund. Do not dispose of the remaining product, as we may need it for quality control purposes.

<x-mail::button :url="config('app.url')">
Contact Pharmacy
</x-mail::button>

If you have already used this medication and are experiencing any adverse effects, please seek medical attention and inform your healthcare provider of the lot number above.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
