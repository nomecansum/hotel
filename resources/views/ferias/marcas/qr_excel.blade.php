<table>
    <tr>
        <th>ID</th>
        <th style="width: 10px">MARCA</th>
        <th style="width: 100px">QR</th>
    </tr>
    @foreach($datos as $dato)
        <tr>
            <td>{{ $dato->id_marca }}</td>
            <td>{{ $dato->des_marca }}</td>
            <td>{{ config('app.url_base_feria').'marcas/'.$dato->token }}</td>
        </tr>
    @endforeach
</table>