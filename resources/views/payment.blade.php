@extends('layouts.main')
@section('content')

<section class="container mt-5 my-3 py-5">
    <div class="container mt-2 text-center">
        <h4>Payment</h4>
        @if (Session::has('total') && Session::get('total') != null)
            @if (Session::has('order_id') && Session::get('order_id') != null)
                
            <h4 style="color: blue;" class="my-5">Total: ${{Session::get('total')}}</h4>
            <!-- Set up a container element for the button -->
            <div id="paypal-button-container"></div>
            @endif
        @endif

    </div>
</section>



<!-- Replace "test" with your own sandbox Business account app client ID -->
<script src="https://www.paypal.com/sdk/js?client-id=Aa9BguN0huhcyMw-MYUcYzAWq8TXbrrug-r4zGhOCtSM4dC7-6RwwBjYtdJaQgHuva0xf8B9zQQd955p&currency=USD"></script>
    
    <script>
      paypal.Buttons({
        // Order is created on the server and the order id is returned
        createOrder() {
          return fetch("/my-server/create-paypal-order", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            // use the "body" param to optionally pass additional order information
            // like product skus and quantities
            body: JSON.stringify({
              cart: [
                {
                  sku: "YOUR_PRODUCT_STOCK_KEEPING_UNIT",
                  quantity: "YOUR_PRODUCT_QUANTITY",
                },
              ],
            }),
          })
          .then((response) => response.json())
          .then((order) => order.id);
        },
        // Finalize the transaction on the server after payer approval
        onApprove(data) {
          return fetch("/my-server/capture-paypal-order", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify({
              orderID: data.orderID
            })
          })
          .then((response) => response.json())
          .then((orderData) => {
            // Successful capture! For dev/demo purposes:
            console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
            const transaction = orderData.purchase_units[0].payments.captures[0];
            alert(`Transaction ${transaction.status}: ${transaction.id}\n\nSee console for all available details`);
            // When ready to go live, remove the alert and show a success message within this page. For example:
            // const element = document.getElementById('paypal-button-container');
            // element.innerHTML = '<h3>Thank you for your payment!</h3>';
            // Or go to another URL:  window.location.href = 'thank_you.html';
          });
        }
      }).render('#paypal-button-container');
    </script>

@endsection