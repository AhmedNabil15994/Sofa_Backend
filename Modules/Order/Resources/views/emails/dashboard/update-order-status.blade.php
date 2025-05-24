@component('mail::message')

    <div dir="rtl" style="text-align:right;">
        <h2>
            <center>تم تغيير حالة الطلب</center>
        </h2>

        <p style="text-align:right;"> اسم العضو : {{ $order->user->name }}</p>
        <p style="text-align:right;"> تاريخ الطلب : {{ $order->created_at }}</p>
        <p style="text-align:right;"> حالة الطلب : {{ $order->orderStatus->title }}</p>

        @if($order->order_notes)
            <p style="text-align:right;"> ملاحظات : {{ $order->order_notes }}</p>
        @endif

        <br>
        <p style="text-align:right;">تطبيق {{ env('APP_NAME') }} يرحب بكم دائما</p>


        <center>
            Thanks,
            <br>
            {{ config('app.name') }}
        </center>
    </div>


@endcomponent
