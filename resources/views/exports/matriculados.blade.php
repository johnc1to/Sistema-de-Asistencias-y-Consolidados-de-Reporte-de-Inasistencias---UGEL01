<table>
    <thead>
        <tr>
            <th colspan="2" style="text-align: center;font-weight: 800;background-color: #afbfd1">Matricula</th>
            <th colspan="7" style="text-align: center;font-weight: 800;background-color: #afbfd1">Datos del estudiante</th>
            <th colspan="7" style="text-align: center;font-weight: 800;background-color: #afbfd1">Programa de Estudio</th>

        </tr>
    <tr>

        <th style="text-align: center;font-weight: 800;width: 12px;background-color: #afbfd1">Cod. Matricula</th>
        <th style="text-align: center;font-weight: 800;width: 15px;background-color: #afbfd1">Fec. Matricula</th>
        <th style="text-align: center;font-weight: 800;width: 10px;background-color: #afbfd1">Tip. Doc.</th>
        <th style="text-align: center;font-weight: 800;width: 12px;background-color: #afbfd1">N° Doc.</th>
        <th style="text-align: center;font-weight: 800;width: 30px;background-color: #afbfd1">Nombre</th>
        <th style="text-align: center;font-weight: 800;width: 20px;background-color: #afbfd1">Ape. Paterno</th>
        <th style="text-align: center;font-weight: 800;width: 20px;background-color: #afbfd1">Ape. Materno</th>
        <th style="text-align: center;font-weight: 800;width: 15px;background-color: #afbfd1">Fec. Nacimiento</th>
        <th style="text-align: center;font-weight: 800;width: 10px;background-color: #afbfd1">Sexo(F/M)</th>,
        <th style="text-align: center;font-weight: 800;width: 10px;background-color: #afbfd1">Cod. Modular Cetpro</th>
        <th style="text-align: center;font-weight: 800;width: 20px;background-color: #afbfd1">Periodo</th>
        <th style="text-align: center;font-weight: 800;width: 10px;background-color: #afbfd1">Turno</th>
        <th style="text-align: center;font-weight: 800;width: 30px;background-color: #afbfd1">Prog. Estudio</th>
        <th style="text-align: center;font-weight: 800;width: 20px;background-color: #afbfd1">Tip. Servicio Educ.</th>
        <th style="text-align: center;font-weight: 800;width: 10px;background-color: #afbfd1">N° creditos</th>
        <th style="text-align: center;font-weight: 800;width: 10px;background-color: #afbfd1">N° Horas</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $invoice)
        <tr>
            <td style="border: 1px solid #1173e4">{{ $invoice->codMat }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->fecMat }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->tipDocAlu }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->docAlu }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->nomAlu }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->apePatAlu }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->apeMatAlu }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->fecNacAlu }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->sexAlu }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->codModOff }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->perOff }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->turOff }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->proEstPee }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->tipSerEduPee }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->credPee }}</td>
            <td style="border: 1px solid #1173e4">{{ $invoice->horPee }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
