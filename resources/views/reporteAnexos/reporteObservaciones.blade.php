<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Reporte de Observaciones Críticas</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 9pt; margin: 0; padding: 0; }
            .header { width: 100%; display: flex; align-items: center; margin-bottom: 10px; }
            .logo { width: 100px; }
            .title { flex: 1; text-align: center; font-size: 14pt; font-weight: bold; color: #d9534f; }
            .subtitle { text-align: center; font-size: 10pt; margin-bottom: 15px; }
            table { border-collapse: collapse; width: 100%; font-size: 8pt; }
            th, td { border: 1px solid #000; padding: 4px; text-align: center; vertical-align: middle; }
            th { background: #f8d7da; color: #721c24; }
            td.red { color: red; font-weight: bold; }
        </style>
    </head>
    <body>
        <!-- Encabezado con banner - VERSIÓN CORREGIDA -->
        <div style="display: flex; align-items: flex-start; gap: 30px; width: 100%; margin-bottom: 15px;">
            <!-- Imagen -->
            <div>
                <img src="https://ventanillavirtual.ugel01.gob.pe/public/img/baner_ugel01.jpg" 
                    style="width: 420px; height: auto;" 
                    alt="Banner UGEL 01">
            </div>
            <!-- Título y fecha en bloque separado -->
            <div style="flex: 1; padding-left: 15px;">
                <div style=" padding: 15px; border-radius: 8px;">
                    <div style="font-size: 14pt; font-weight: bold; color: #d9534f; text-align: center; margin-bottom: 10px;">
                        REPORTE DE OBSERVACIONES CRÍTICAS DE DOCENTES
                    </div>
                    <div style="font-size: 10pt; text-align: right; color: #666;">
                        UGEL 01 - Fecha de emisión: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            </div>
        </div>
        <!-- Tabla -->
        <table>
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>EXPEDIENTE Nº</th>
                    <th>ESCRITURA</th>
                    <th>FLS.</th>
                    <th>APELLIDOS Y NOMBRES</th>
                    <th>CÓDIGO MODULAR</th>
                    <th>NIVEL EDUCATIVO</th>
                    <th>II. EE.</th>
                    <th>DISTRITO</th>
                    <th>CARGO</th>
                    <th>SITUACIÓN LABORAL</th>
                    <th>CÓDIGO DE PLAZA</th>
                    <th>OFICIO</th>
                    <th>INF. ESC.</th>
                    <th>INF. MÉDICO/INF. ARH-EDBTH MEMORANDUM</th>
                    <th>C.I.T.T. Nº</th>
                    <th>DIAS DE L.C.G. DE HABER POR ENFERMEDAD ACUMULADO</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>C/G</th>
                    <th>S/G</th>
                    <th>C/S</th>
                    <th>Días de Licencia</th>
                </tr>
            </thead>
            <tbody>
                @foreach($observacionesCriticas as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->expediente }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ $item->nombres }}</td>
                    <td>{{ $item->codmodce }}</td>
                    <td>{{ $item->nivel }}</td>
                    <td>{{ $item->institucion ?? '' }}</td>
                    <td>{{ $item->distrito_iiee ?? '' }}</td>
                    <td>{{ $item->cargo }}</td>
                    <td>{{ $item->condicion }}</td>
                    <td>{{ $item->codplaza }}</td>
                    <td>{{ $item->oficio }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $item->fecha_inicio ? \Carbon\Carbon::parse($item->fecha_inicio)->format('d/m/Y') : '' }}</td>
                    <td>{{ $item->fecha_fin ? \Carbon\Carbon::parse($item->fecha_fin)->format('d/m/Y') : '' }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $item->dias_licencia ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </body>
</html>
