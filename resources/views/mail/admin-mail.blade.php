

{{ ucfirst($user->name )}} has ordered these,<br>
<ul>
    @foreach($details as $detail)
        <li>
            {{getProductName( $detail->product_id) }}
        </li>
    @endforeach

</ul>
response this as soon as possible .
