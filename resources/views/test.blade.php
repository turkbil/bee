<!DOCTYPE html>
<html>
<head>
    <title>Helper Test</title>
</head>
<body>
    <h1>Helper Test</h1>
    
    <table border="1">
        <tr>
            <th>Fonksiyon</th>
            <th>Durum</th>
        </tr>
        @foreach($results as $function => $exists)
        <tr>
            <td>{{ $function }}</td>
            <td style="color: {{ $exists ? 'green' : 'red' }}">
                {{ $exists ? 'Mevcut' : 'Bulunamadı' }}
            </td>
        </tr>
        @endforeach
    </table>
</body>
</html>