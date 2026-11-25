Dear {{ auth()->user()->name }} ,<br>

     your items ,<br>
<ul>
    @foreach($details as $detail)
        <li>
            {{getProductName( $detail->product_id) }}
        </li>
    @endforeach

</ul>
has been ordered and wait for the response .<br>

Thank You !!!<br>
-{{ getSiteTitle() }}
