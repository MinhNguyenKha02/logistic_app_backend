<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report with Statistic</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        #section{
            background: white;
            color: black;
            padding: 16px;
            margin-bottom: 1rem;
        }
        #section p{
            margin-bottom: 1rem;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            color: black;
            margin-bottom: 1rem;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 16px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        #title{
            background: white;
            color: rgb(255 90 31);
            padding: 16px 16px 0 16px;
            margin-bottom: 0;
        }
    </style>
</head>
<body style="background: rgb(255 90 31); color:white; padding:1rem;">
<H3 id="title">General in weekly days</H3>
<section id="section">
    <p>Products: {{ $statistics['products'] }}</p>
    <p>Inventories: {{ $statistics['inventories'] }}</p>
    <p>Transactions: {{ $statistics['transactions'] }}</p>
    <p>Orders: {{ $statistics['orders'] }}</p>
    <p>Return Orders: {{ $statistics['return_orders'] }}</p>
    <p>Revenue: {{ $statistics['revenue'] }}</p>
</section>

<table>
    <thead>
    <tr>
        <th>Day</th>
        <th>Orders Per Day</th>
        <th>Return Orders Per Day</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($statistics['ordersPerDay'] as $index => $order)
        <tr>
            <td>{{ $statistics['days'][$index] ?? 'Unknown' }}</td>
            <td>{{ $order }}</td>
            <td>{{ $statistics['returnOrdersPerDay'][$index] ?? 0 }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<span>&copy; By Minh Nguyen</span>
</body>
</html>
