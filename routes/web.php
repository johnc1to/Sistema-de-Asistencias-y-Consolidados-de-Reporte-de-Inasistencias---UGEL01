<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Notificacion;
use App\Http\Controllers\NotificacionPrueba;
use App\Http\Controllers\Login;
use App\Http\Controllers\Trabajoremoto;
use App\Http\Controllers\Rrhh;
use App\Mail\Notificarcorreo;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Fichamonitoreo;
use App\Http\Controllers\Vacacionesie;
use App\Http\Controllers\Boletacesante;
use App\Http\Controllers\Normas;
use App\Http\Controllers\Soporte;
use App\Http\Controllers\Racio;
use App\Http\Controllers\Visacion;
use App\Http\Controllers\Citamedica;
use App\Http\Controllers\Cetpro;
use App\Http\Controllers\Consola;
use App\Http\Controllers\Epps;
use App\Http\Controllers\EquiposInformaticos;
use App\Http\Controllers\Office365;
use App\Http\Controllers\Excel;
use App\Http\Controllers\Excel2;
use App\Http\Controllers\Qr;
use App\Http\Controllers\Practicas;
use App\Http\Controllers\Areas;
use App\Http\Controllers\Materiales;
use App\Http\Controllers\Contactos;
use App\Http\Controllers\Plazasvacantes;
use App\Http\Controllers\bienes\BienesUser;
use App\Http\Controllers\bienes\RefirmaInvokerController;
use App\Http\Controllers\AprobacionTituloAuxiliarTecnicoController;
use App\Http\Controllers\NumeracionTituloUgelController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\SituacionLaboralController;
use App\Http\Controllers\Tipodocumentos;
use App\Http\Controllers\Webservices;
use App\Http\Controllers\Receptores;
use App\Http\Controllers\asistencia\AsistenciaTeletrabajo;
use App\Http\Controllers\bienes\RefirmaInvokerNoPatrimonialController;
use App\Http\Controllers\bienes\BienesUserNoPatrimonial;
use App\Http\Controllers\DocumentosRemitidos;
use App\Http\Controllers\Siseaprende2025;
use App\Http\Controllers\Restablecer_correo;
use App\Http\Controllers\Anexo03Controller;
use App\Http\Controllers\Anexo04Controller;
use App\Http\Controllers\ReporteAnexosController;
use App\Http\Controllers\PlantillaReporte;
use App\Http\Controllers\Plataforma;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('sin_permisos', function () {
    return view('login/sin_permisos');
});

Route::get('sin_permisos_modulo', function () {
    return view('login/sin_permisos_modulo');
});



Route::get('listar_logosie',[DocumentosRemitidos::class,'listar_logosie'])->name('listar_logosie');

Route::get('listar_qr',[Qr::class,'listar_qr'])->name('listar_qr');

Route::get('epps_coodinador',[Epps::class,'epps_coodinador'])->name('epps_coodinador');
Route::get('epps_director',[Epps::class,'epps_director'])->name('epps_director');
Route::post('get_epps',[Epps::class,'get_epps'])->name('get_epps');
Route::post('guardar_epps',[Epps::class,'guardar_epps'])->name('guardar_epps');

Route::get('epps_resumen',[Epps::class,'epps_resumen'])->name('epps_resumen');
Route::post('get_resumen',[Epps::class,'get_resumen'])->name('get_resumen');
Route::get('excel_epps',[Excel::class,'excel_epps'])->name('excel_epps');
Route::get('fecha',[Excel::class,'fecha'])->name('fecha');

Route::get('excel_ie_que_faltan_evaluacion_primaria',[Excel2::class,'excel_ie_que_faltan_evaluacion_primaria'])->name('excel_ie_que_faltan_evaluacion_primaria');
Route::get('excel_ie_que_faltan_evaluacion_primaria_4_5_6',[Excel2::class,'excel_ie_que_faltan_evaluacion_primaria_4_5_6'])->name('excel_ie_que_faltan_evaluacion_primaria_4_5_6');
Route::get('excel_ie_que_faltan_evaluacion_secundaria',[Excel2::class,'excel_ie_que_faltan_evaluacion_secundaria'])->name('excel_ie_que_faltan_evaluacion_secundaria');

Route::get('cas_descuento',[Excel2::class,'cas_descuento'])->name('cas_descuento');

Route::get('inv_registro',[EquiposInformaticos::class,'inv_registro'])->name('inv_registro');
Route::post('get_inv',[EquiposInformaticos::class,'get_inv'])->name('get_inv');
Route::post('guardar_inv',[EquiposInformaticos::class,'guardar_inv'])->name('guardar_inv');

Route::get('inv_resumen',[EquiposInformaticos::class,'inv_resumen'])->name('inv_resumen');
Route::post('get_inv_resumen',[EquiposInformaticos::class,'get_inv_resumen'])->name('get_inv_resumen');
Route::get('excel_equipos_informaticos',[Excel::class,'excel_equipos_informaticos'])->name('excel_equipos_informaticos');

Route::get('consultaUser',[Office365::class,'consultaUser'])->name('consultaUser');

Route::get('office365_aplicacion',[Office365::class,'office365_aplicacion'])->name('office365_aplicacion');
Route::get('callback_aplicacion',[Office365::class,'callback_aplicacion'])->name('callback_aplicacion');

Route::get('office365_adjudicacion',[Office365::class,'office365_adjudicacion'])->name('office365_adjudicacion');
Route::get('callback_adjudicacion',[Office365::class,'callback_adjudicacion'])->name('callback_adjudicacion');

Route::get('office365_encargatura',[Office365::class,'office365_encargatura'])->name('office365_encargatura');
Route::get('callback_encargatura',[Office365::class,'callback_encargatura'])->name('callback_encargatura');

Route::get('office365_sicab',[Office365::class,'office365_sicab'])->name('office365_sicab');
Route::get('callback_sicab',[Office365::class,'callback_sicab'])->name('callback_sicab');




Route::get('consola',[Consola::class,'consola'])->name('consola');
Route::get('distribucionventanillas',[Consola::class,'distribucionventanillas'])->name('distribucionventanillas');
Route::post('guardarconsola',[Consola::class,'guardarconsola'])->name('guardarconsola');


Route::get('verprogramas',[Cetpro::class,'verprogramas'])->name('verprogramas');
Route::get('listarprogramas',[Cetpro::class,'listarprogramas'])->name('listarprogramas');
Route::get('listar_Auxiliar',[Cetpro::class,'listar_Auxiliar'])->name('listar_Auxiliar');

Route::get('solicitarcitamedica',[Citamedica::class,'solicitarcitamedica'])->name('solicitarcitamedica');
Route::get('listarcitamedica',[Citamedica::class,'listarcitamedica'])->name('listarcitamedica');
Route::post('guardarcitamedica',[Citamedica::class,'guardarcitamedica'])->name('guardarcitamedica');
Route::get('siguiente_cita',[Citamedica::class,'siguiente_cita'])->name('siguiente_cita');
Route::get('reportecitamedica',[Citamedica::class,'reportecitamedica'])->name('reportecitamedica');
Route::get('consultareportecitamedica',[Citamedica::class,'consultareportecitamedica'])->name('consultareportecitamedica');
Route::get('listarreportecitamedica',[Citamedica::class,'listarreportecitamedica'])->name('listarreportecitamedica');
Route::get('listarresumencitamedica',[Citamedica::class,'listarresumencitamedica'])->name('listarresumencitamedica');
Route::get('vercitamedica',[Citamedica::class,'vercitamedica'])->name('vercitamedica');
Route::post('editarcitamedica',[Citamedica::class,'editarcitamedica'])->name('editarcitamedica');
Route::get('pdf_informemedico',[Citamedica::class,'pdf_informemedico'])->name('pdf_informemedico');


Route::get('enviarmailvis',[Visacion::class,'enviarmailvis'])->name('enviarmailvis');
Route::get('session',[Visacion::class,'session'])->name('session');
Route::get('certificadodeestudio',[Visacion::class,'certificadodeestudio'])->name('certificadodeestudio');
Route::get('ver_certificadodeestudio',[Visacion::class,'ver_certificadodeestudio'])->name('ver_certificadodeestudio');
Route::get('solicitarsubsanarcertificado',[Visacion::class,'solicitarsubsanarcertificado'])->name('solicitarsubsanarcertificado');
Route::get('citarciudadano',[Visacion::class,'citarciudadano'])->name('citarciudadano');
Route::post('guardarsubsanarcertificado',[Visacion::class,'guardarsubsanarcertificado'])->name('guardarsubsanarcertificado');
Route::post('guardarcitarciudadano',[Visacion::class,'guardarcitarciudadano'])->name('guardarcitarciudadano');
Route::get('recepcionar_certificado',[Visacion::class,'recepcionar_certificado'])->name('recepcionar_certificado');
Route::get('archivar_certificado',[Visacion::class,'archivar_certificado'])->name('archivar_certificado');
Route::get('citarciudadano',[Visacion::class,'citarciudadano'])->name('citarciudadano');

Route::get('reqseccincremento',[Racio::class,'index'])->name('reqseccincremento');
Route::get('verreqseccincrementoie',[Racio::class,'verreqseccincrementoie'])->name('verreqseccincrementoie');
Route::get('verreqseccincrementosecciones',[Racio::class,'verreqseccincrementosecciones'])->name('verreqseccincrementosecciones');
Route::get('anexo_racio_reqseccincrementosecciones',[Excel::class,'anexo_racio_reqseccincrementosecciones'])->name('anexo_racio_reqseccincrementosecciones');
Route::get('excel_postulantes',[Excel::class,'excel_postulantes'])->name('excel_postulantes');
Route::get('excel_registro_matricula_cetpro',[Excel::class,'excel_registro_matricula_cetpro'])->name('excel_registro_matricula_cetpro');
Route::get('excel_registro_matricula_modulos_cetpro',[Excel::class,'excel_registro_matricula_modulos_cetpro'])->name('excel_registro_matricula_modulos_cetpro');
Route::get('excel_registro_titulados_cetpro',[Excel::class,'excel_registro_titulados_cetpro'])->name('excel_registro_titulados_cetpro');

Route::get('anexo05director',[Excel::class,'anexo05director'])->name('anexo05director');
Route::get('anexo06docentes',[Excel::class,'anexo06docentes'])->name('anexo06docentes');
Route::get('anexo07estudiantes',[Excel::class,'anexo07estudiantes'])->name('anexo07estudiantes');
Route::get('anexo08reportemensual',[Excel::class,'anexo08reportemensual'])->name('anexo08reportemensual');
Route::get('anexo09actualizaciondirectores',[Excel::class,'anexo09actualizaciondirectores'])->name('anexo09actualizaciondirectores');
Route::get('anexotraslado',[Excel::class,'anexotraslado'])->name('anexotraslado');
Route::get('revisarsoporte',[Soporte::class,'revisarsoporte'])->name('revisarsoporte');
Route::get('versolicitudesesp',[Soporte::class,'versolicitudesesp'])->name('versolicitudesesp');
Route::post('guardarrespuesta',[Soporte::class,'guardarrespuesta'])->name('guardarrespuesta');
Route::post('guardaraccesoestudiante',[Soporte::class,'guardaraccesoestudiante'])->name('guardaraccesoestudiante');
Route::post('guardaraccesodocente',[Soporte::class,'guardaraccesodocente'])->name('guardaraccesodocente');
Route::get('solicitarsoporte',[Soporte::class,'solicitarsoporte'])->name('solicitarsoporte');
Route::get('popup_accesodocente',[Soporte::class,'popup_accesodocente'])->name('popup_accesodocente');
Route::post('guardar_soporte',[Soporte::class,'guardar_soporte'])->name('guardar_soporte');
Route::get('versolicitudes',[Soporte::class,'versolicitudes'])->name('versolicitudes');
Route::get('popup_restableceraccesoestudiante',[Soporte::class,'popup_restableceraccesoestudiante'])->name('popup_restableceraccesoestudiante');
Route::get('popup_crearaccesodocente',[Soporte::class,'popup_crearaccesodocente'])->name('popup_crearaccesodocente');
Route::get('popup_crearaccesoestudiante',[Soporte::class,'popup_crearaccesoestudiante'])->name('popup_crearaccesoestudiante');
Route::get('popup_crearaccesodirector',[Soporte::class,'popup_crearaccesodirector'])->name('popup_crearaccesodirector');
Route::get('popup_restableceracesodirector',[Soporte::class,'popup_restableceracesodirector'])->name('popup_restableceracesodirector');
Route::get('popup_actualizaciondirector',[Soporte::class,'popup_actualizaciondirector'])->name('popup_actualizaciondirector');
Route::get('eliminarsolicitud',[Soporte::class,'eliminarsolicitud'])->name('eliminarsolicitud');
Route::get('popup_masivorestableceraccesodocente',[Soporte::class,'popup_masivorestableceraccesodocente'])->name('popup_masivorestableceraccesodocente');
Route::get('popup_masivocrearaccesodocente',[Soporte::class,'popup_masivocrearaccesodocente'])->name('popup_masivocrearaccesodocente');
Route::get('popup_alerta',[Soporte::class,'popup_alerta'])->name('popup_alerta');

Route::get('pruebacopy',[Notificacion::class,'prueba'])->name('pruebacopy');
Route::get('buscarMultipleNotificaciones',[Notificacion::class,'buscarMultipleNotificaciones'])->name('buscarMultipleNotificaciones');
Route::get('buscarciudadano',[Notificacion::class,'buscarciudadano'])->name('buscarciudadano');
Route::get('notificarciudadano',[Notificacion::class,'notificarciudadano'])->name('notificarciudadano');
Route::get('enviarmail',[Notificacion::class,'enviarmail'])->name('enviarmail');
Route::get('aaa',[Notificacion::class,'aaa'])->name('aaa');

Route::get('pruebaprueba',[NotificacionPrueba::class,'prueba'])->name('pruebaprueba');
Route::get('buscarMultipleNotificacionesprueba',[NotificacionPrueba::class,'buscarMultipleNotificaciones'])->name('buscarMultipleNotificacionesprueba');
Route::get('buscarciudadanoprueba',[NotificacionPrueba::class,'buscarciudadano'])->name('buscarciudadanoprueba');
Route::get('notificarciudadanoprueba',[NotificacionPrueba::class,'notificarciudadano'])->name('notificarciudadanoprueba');
Route::get('enviarmailprueba',[NotificacionPrueba::class,'enviarmail'])->name('enviarmailprueba');
Route::get('aaaprueba',[NotificacionPrueba::class,'aaa'])->name('aaaprueba');


Route::get('ingreso_esp',[Login::class,'ingreso_esp'])->name('ingreso_esp');
Route::get('ingreso_dir',[Login::class,'ingreso_dir'])->name('ingreso_dir');
Route::get('versession',[Login::class,'versession'])->name('versession');
Route::get('cerrarsession',[Login::class,'cerrarsession'])->name('cerrarsession');

Route::get('asistenciaoficina',[Trabajoremoto::class,'asistenciaoficina'])->name('asistenciaoficina');
Route::get('asistenciaoficina_area',[Trabajoremoto::class,'asistenciaoficina_area'])->name('asistenciaoficina_area');
Route::get('ver_asistenciaoficina',[Trabajoremoto::class,'ver_asistenciaoficina'])->name('ver_asistenciaoficina');

Route::get('consolidado_trabajo',[Trabajoremoto::class,'consolidado_trabajo'])->name('consolidado_trabajo');
Route::get('ver_consolidado_trabajo',[Trabajoremoto::class,'ver_consolidado_trabajo'])->name('ver_consolidado_trabajo');
Route::get('reporte_consolidado_trabajo',[Trabajoremoto::class,'reporte_consolidado_trabajo'])->name('reporte_consolidado_trabajo');
Route::get('excel_consolidado_trabajo',[Trabajoremoto::class,'excel_consolidado_trabajo'])->name('excel_consolidado_trabajo');
Route::post('guardar_consolidado_trabajo',[Trabajoremoto::class,'guardar_consolidado_trabajo'])->name('guardar_consolidado_trabajo');
Route::post('guardar_consolidadotrabajo_dias',[Trabajoremoto::class,'guardar_consolidadotrabajo_dias'])->name('guardar_consolidadotrabajo_dias');
Route::get('pdfreporteasistenciamensual',[Trabajoremoto::class,'pdfreporteasistenciamensual'])->name('pdfreporteasistenciamensual');
Route::get('pdfreporteasistencia',[Trabajoremoto::class,'pdfreporteasistencia'])->name('pdfreporteasistencia');
Route::post('firmarreporteasistencia',[Trabajoremoto::class,'firmarreporteasistencia'])->name('firmarreporteasistencia');
Route::get('firmar_descargar',[Trabajoremoto::class,'firmar_descargar'])->name('firmar_descargar');
Route::post('firmarreporteasistencia',[Trabajoremoto::class,'firmarreporteasistencia'])->name('firmarreporteasistencia');
Route::post('subirfirma',[Trabajoremoto::class,'subirfirma'])->name('subirfirma');


Route::get('popup_especialista',[Rrhh::class,'popup_especialista'])->name('popup_especialista');
Route::get('validar_dni_especialista',[Rrhh::class,'validar_dni_especialista'])->name('validar_dni_especialista');
Route::post('guardar_especialista',[Rrhh::class,'guardar_especialista'])->name('guardar_especialista');
Route::get('eliminarespecialista',[Rrhh::class,'eliminarespecialista'])->name('eliminarespecialista');

Route::get('prueba',[Trabajoremoto::class,'prueba'])->name('prueba');

Route::get('nuevanorma', [Normas::class,'nuevanorma'])->name('nuevanorma');
Route::get('popup_anadirnorma', [Normas::class,'popup_anadirnorma'])->name('popup_anadirnorma');
Route::post('guardarnorma', [Normas::class,'guardarnorma'])->name('guardarnorma');
Route::get('normasentidades',[Normas::class,'normasentidades'])->name('normasentidades');
Route::get('normastemas',[Normas::class,'normastemas'])->name('normastemas');
Route::get('normastipos',[Normas::class,'normastipos'])->name('normastipos');
Route::get('normassituacion',[Normas::class,'normassituacion'])->name('normassituacion');
Route::get('normasrepositorio',[Normas::class,'normasrepositorio'])->name('normasrepositorio');
Route::post('guardarnormasentidades',[Normas::class,'guardarnormasentidades'])->name('guardarnormasentidades');
Route::post('guardarnormastemas',[Normas::class,'guardarnormastemas'])->name('guardarnormastemas');
Route::post('guardarnormastipos',[Normas::class,'guardarnormastipos'])->name('guardarnormastipos');
Route::post('guardarnormassituacion',[Normas::class,'guardarnormassituacion'])->name('guardarnormassituacion');
Route::get('eliminarnorma',[Normas::class,'eliminarnorma'])->name('eliminarnorma');
Route::get('buscarnormas',[Normas::class,'buscarnormas'])->name('buscarnormas');
Route::get('normalink',[Normas::class,'normalink'])->name('normalink');
Route::get('normanbuscarnroFnn',[Normas::class,'normanbuscarnroFnn'])->name('normanbuscarnroFnn');
Route::get('consultaarchivo', [Normas::class,'consultaarchivo'])->name('consultaarchivo');

Route::get('crearficha',[Fichamonitoreo::class,'crearficha'])->name('crearficha');
Route::get('listar_ficha',[Fichamonitoreo::class,'listar_ficha'])->name('listar_ficha');
Route::post('guardar_ficha',[Fichamonitoreo::class,'guardar_ficha'])->name('guardar_ficha');
Route::get('listar_pregunta',[Fichamonitoreo::class,'listar_pregunta'])->name('listar_pregunta');
Route::post('guardar_pregunta',[Fichamonitoreo::class,'guardar_pregunta'])->name('guardar_pregunta');
Route::get('listar_ie_respuesta',[Fichamonitoreo::class,'listar_ie_respuesta'])->name('listar_ie_respuesta');
Route::get('listar_html_adicional',[Fichamonitoreo::class,'listar_html_adicional'])->name('listar_html_adicional');
Route::post('guardar_html_adicional',[Fichamonitoreo::class,'guardar_html_adicional'])->name('guardar_html_adicional');
Route::get('mostrar_modelo_ficha',[Fichamonitoreo::class,'mostrar_modelo_ficha'])->name('mostrar_modelo_ficha');
Route::post('guardar_respuesta',[Fichamonitoreo::class,'guardar_respuesta'])->name('guardar_respuesta');
Route::get('enviar_ficha_ugel01',[Fichamonitoreo::class,'enviar_ficha_ugel01'])->name('enviar_ficha_ugel01');
Route::get('director',[Fichamonitoreo::class,'director'])->name('director');
Route::get('ver_ficha_ie',[Fichamonitoreo::class,'ver_ficha_ie'])->name('ver_ficha_ie');
Route::get('mostrar_ficha',[Fichamonitoreo::class,'mostrar_ficha'])->name('mostrar_ficha');
Route::get('mostrar_pdf_ficha',[Fichamonitoreo::class,'mostrar_pdf_ficha'])->name('mostrar_pdf_ficha');
Route::get('exportar_sustento_ficha',[Fichamonitoreo::class,'exportar_sustento_ficha'])->name('exportar_sustento_ficha');
Route::get('generar_masa_pdf_ficha',[Fichamonitoreo::class,'generar_masa_pdf_ficha'])->name('generar_masa_pdf_ficha');
Route::get('eliminar_ficha',[Fichamonitoreo::class,'eliminar_ficha'])->name('eliminar_ficha');
Route::get('completar_nro_recurso',[Fichamonitoreo::class,'completar_nro_recurso'])->name('completar_nro_recurso');


Route::post('guardar_docente',[Fichamonitoreo::class,'guardar_docente'])->name('guardar_docente');
Route::post('guardar_receptores',[Fichamonitoreo::class,'guardar_receptores'])->name('guardar_receptores');
Route::post('guardar_solo_receptores',[Fichamonitoreo::class,'guardar_solo_receptores'])->name('guardar_solo_receptores');
Route::post('guardar_observaciones',[Fichamonitoreo::class,'guardar_observaciones'])->name('guardar_observaciones');
Route::get('popup_anadirfichadirector',[Fichamonitoreo::class,'popup_anadirfichadirector'])->name('popup_anadirfichadirector');
Route::post('anadirfichadirector',[Fichamonitoreo::class,'anadirfichadirector'])->name('anadirfichadirector');
Route::get('popup_anadirfichaesp',[Fichamonitoreo::class,'popup_anadirfichaesp'])->name('popup_anadirfichaesp');
Route::post('anadirfichaespecialista',[Fichamonitoreo::class,'anadirfichaespecialista'])->name('anadirfichaespecialista');
Route::get('docentevalidar_dni',[Fichamonitoreo::class,'docentevalidar_dni'])->name('docentevalidar_dni');
Route::get('exportar_respuestas_ficha',[Excel::class,'exportar_respuestas_ficha'])->name('exportar_respuestas_ficha');

Route::get('popup_anadirfichaesp_iiee',[Fichamonitoreo::class,'popup_anadirfichaesp_iiee'])->name('popup_anadirfichaesp_iiee');
Route::post('anadirfichaespecialista_iiee',[Fichamonitoreo::class,'anadirfichaespecialista_iiee'])->name('anadirfichaespecialista_iiee');

Route::get('/listar-iiee-faltantes', [Fichamonitoreo::class, 'listarIieeFaltantes'])->name('iieefaltante');;

Route::get('vacacionesdirector',[Vacacionesie::class,'director'])->name('vacacionesdirector');
Route::get('eliminarVacaciondirector',[Vacacionesie::class,'eliminarVacaciondirector'])->name('eliminarVacaciondirector');
Route::get('guardarVacacionesdir',[Vacacionesie::class,'guardarVacacionesdir'])->name('guardarVacacionesdir');
Route::get('vacacionesadministrativo',[Vacacionesie::class,'administrativo'])->name('vacacionesadministrativo');
Route::get('guardarVacacionesAdmin',[Vacacionesie::class,'guardarVacacionesAdmin'])->name('guardarVacacionesAdmin');
Route::get('eliminarVacacionadmin',[Vacacionesie::class,'eliminarVacacionadmin'])->name('eliminarVacacionadmin');


Route::get('boletacesante',[Boletacesante::class,'index'])->name('boletacesante');
Route::post('subirarchivoboleta',[Boletacesante::class,'subirarchivoboleta'])->name('subirarchivoboleta');
Route::get('listararchivosboleta',[Boletacesante::class,'listararchivosboleta'])->name('listararchivosboleta');
Route::get('listarboletas',[Boletacesante::class,'listarboletas'])->name('listarboletas');
Route::get('eliminarboletas',[Boletacesante::class,'eliminarboletas'])->name('eliminarboletas');
Route::get('pdf_boleta',[Boletacesante::class,'pdf_boleta'])->name('pdf_boleta');
Route::get('generarboletas',[Boletacesante::class,'generarboletas'])->name('generarboletas');
Route::get('descargarboletasfirmadas',[Boletacesante::class,'descargarboletasfirmadas'])->name('descargarboletasfirmadas');

Route::get('Codigo',[Qr::class,'Codigo'])->name('Codigo');
Route::get('listar_qr',[Qr::class,'listar_qr'])->name('listar_qr');
Route::post('guardar_qr',[Qr::class,'guardar_qr'])->name('guardar_qr');
Route::get('tabla_qr',[Qr::class,'tabla_qr'])->name('tabla_qr');
Route::get('eliminar_qr',[Qr::class,'eliminar_qr'])->name('eliminar_qr');
Route::get('reporte_qr',[Qr::class,'reporte_qr'])->name('reporte_qr');
Route::get('grafico_qr',[Qr::class,'grafico_qr'])->name('grafico_qr');
Route::get('acceso_directo',[Qr::class,'acceso_directo'])->name('acceso_directo');

Route::get('listar_Practicas',[Practicas::class,'listar_Practicas'])->name('listar_Practicas');
Route::get('tabla_Practicas',[Practicas::class,'tabla_Practicas'])->name('tabla_Practicas');
Route::post('guardar_Practicas',[Practicas::class,'guardar_Practicas'])->name('guardar_Practicas');
Route::get('eliminar_Practicas',[Practicas::class,'eliminar_Practicas'])->name('eliminar_Practicas');

Route::get('listar_Categorias',[Practicas::class,'listar_Categorias'])->name('listar_Categorias');
Route::get('tabla_Categorias',[Practicas::class,'tabla_Categorias'])->name('tabla_Categorias');
Route::post('guardar_Categorias',[Practicas::class,'guardar_Categorias'])->name('guardar_Categorias');
Route::get('eliminar_Categorias',[Practicas::class,'eliminar_Categorias'])->name('eliminar_Categorias');

/**REGISTRO DE MATERIAL EDUCATIVO**/
Route::get('listar_Area',[Areas::class,'listar_Area'])->name('listar_Area');
Route::post('guardar_Area',[Areas::class,'guardar_Area'])->name('guardar_Area');
Route::get('eliminar_Area',[Areas::class,'eliminar_Area'])->name('eliminar_Area');

Route::get('listar_Material',[Materiales::class,'listar_Material'])->name('listar_Material');
Route::get('Registro_Material',[Materiales::class,'Registro_Material'])->name('Registro_Material');
Route::get('tabla_Material',[Materiales::class,'tabla_Material'])->name('tabla_Material');
Route::post('guardar_Material',[Materiales::class,'guardar_Material'])->name('guardar_Material');
Route::post('guardar_Material_Masivo',[Materiales::class,'guardar_Material_Masivo'])->name('guardar_Material_Masivo');
Route::get('eliminar_Material',[Materiales::class,'eliminar_Material'])->name('eliminar_Material');
Route::get('reporte_material',[Materiales::class,'reporte_material'])->name('reporte_material');
Route::get('tabla_Reporte',[Materiales::class,'tabla_Reporte'])->name('tabla_Reporte');
Route::get('reporte_material_pbi',[Materiales::class,'reporte_material_pbi'])->name('reporte_material_pbi');

Route::get('listar_Contactos',[Contactos::class,'listar_Contactos'])->name('listar_Contactos');
Route::get('tabla_Contactos',[Contactos::class,'tabla_Contactos'])->name('tabla_Contactos');
Route::get('tabla_Contactos_elimados',[Contactos::class,'tabla_Contactos_elimados'])->name('tabla_Contactos_elimados');
Route::post('guardar_Contactos',[Contactos::class,'guardar_Contactos'])->name('guardar_Contactos');
Route::get('eliminar_Contactos',[Contactos::class,'eliminar_Contactos'])->name('eliminar_Contactos');
Route::get('contrasena_Contactos',[Contactos::class,'contrasena_Contactos'])->name('contrasena_Contactos');

/**REGISTRO DE MATERIAL EDUCATIVO**/

/**inicio JMMJ 14-06-2023 */
Route::get("TituloEspecialistaUgel",[AprobacionTituloAuxiliarTecnicoController::class,"index"])->name("titulo_especialista_ugel");
Route::post("tituladosCetpro",[AprobacionTituloAuxiliarTecnicoController::class,"store"])->name("titulados_cetpro");
Route::post("viewTitulo",[AprobacionTituloAuxiliarTecnicoController::class,"titulo"])->name("view_titulo");
Route::post("saveObservacionTitulo",[AprobacionTituloAuxiliarTecnicoController::class,"saveObservacionTitulo"])->name("saveObservacionTitulo");
Route::post("saveAprobarTitulo",[AprobacionTituloAuxiliarTecnicoController::class,"saveAprobarTitulo"])->name("saveAprobarTitulo");

Route::get("numeracion",[NumeracionTituloUgelController::class,"index"])->name("numeracion");
Route::post("consulta-titulo-aprobado",[NumeracionTituloUgelController::class,"store"])->name("tituloAprobado");
Route::post("save-numeracion-ugel",[NumeracionTituloUgelController::class,"create"])->name("saveNumeracion");

Route::get("reporte",[ReportesController::class,"index"])->name("Reporte");
Route::post("reporte",[ReportesController::class,"store"])->name("getReporte");
Route::get('export', [ReportesController::class, 'show'])->name("exportExcel");
Route::get('export1', [ReportesController::class, 'exportsTitulados'])->name("exportsTitulados");
Route::get('export2', [ReportesController::class, 'exportsCertificados'])->name("exportsCertificados");

Route::get('alertarplazasvacantes', [Plazasvacantes::class, 'alertarplazasvacantes'])->name("alertarplazasvacantes");
Route::get('tabla_alertarplazasvacantes', [Plazasvacantes::class, 'tabla_alertarplazasvacantes'])->name("tabla_alertarplazasvacantes");
Route::post('guardar_alertarplazasvacantes', [Plazasvacantes::class, 'guardar_alertarplazasvacantes'])->name("guardar_alertarplazasvacantes");
Route::get('eliminar_alertarplazasvacantes', [Plazasvacantes::class, 'eliminar_alertarplazasvacantes'])->name("eliminar_alertarplazasvacantes");
Route::get('reporteplazasvacantes', [Plazasvacantes::class, 'reporteplazasvacantes'])->name("reporteplazasvacantes");
Route::get('tabla_reporteplazasvacantes', [Plazasvacantes::class, 'tabla_reporteplazasvacantes'])->name("tabla_reporteplazasvacantes");
Route::post('guardar_reporteplazasvacantes', [Plazasvacantes::class, 'guardar_reporteplazasvacantes'])->name("guardar_reporteplazasvacantes");



Route::post('save-seguimiento-estudiante', [SituacionLaboralController::class, 'store'])->name("saveSeguimientoEstudiante");
Route::post('getHistory', [SituacionLaboralController::class, 'history'])->name("getHistory");
/**Fin JMMJ 14-06-2023 */

Route::get('listar_tipodocumento',[Tipodocumentos::class,'listar_tipodocumento'])->name('listar_tipodocumento');
Route::get('tabla_tipodocumento',[Tipodocumentos::class,'tabla_tipodocumento'])->name('tabla_tipodocumento');
Route::post('guardar_tipodocumento',[Tipodocumentos::class,'guardar_tipodocumento'])->name('guardar_tipodocumento');
Route::get('eliminar_tipodocumento',[Tipodocumentos::class,'eliminar_tipodocumento'])->name('eliminar_tipodocumento');


Route::get('reportecorreos',[Restablecer_correo::class,'reportecorreos'])->name('reportecorreos');
Route::get('tabla_reportecorreos',[Restablecer_correo::class,'tabla_reportecorreos'])->name('tabla_reportecorreos');
Route::post('guardar_reportecorreos',[Restablecer_correo::class,'guardar_reportecorreos'])->name('guardar_reportecorreos');
Route::get('correo',[Restablecer_correo::class,'correo'])->name('correo');
Route::get('excel_reportecorreos',[Excel::class,'excel_reportecorreos'])->name('excel_reportecorreos');

//Route::get('consulta_app_modulos',[Webservices::class,'consulta_app_modulos'])->name('consulta_app_modulos');
Route::post('consulta_app_modulos',[Webservices::class,'consulta_app_modulos'])->name('consulta_app_modulos');
Route::post('consulta_especialistas',[Webservices::class,'consulta_especialistas'])->name('consulta_especialistas');
Route::get('nexus_dir',[Webservices::class,'nexus_dir'])->name('nexus_dir');
Route::get('url',[Webservices::class,'url'])->name('url');

Route::get('listar_receptor',[Receptores::class,'listar_receptor'])->name('listar_receptor');
Route::get('tabla_receptor',[Receptores::class,'tabla_receptor'])->name('tabla_receptor');
Route::post('guardar_receptor',[Receptores::class,'guardar_receptor'])->name('guardar_receptor');
Route::get('eliminar_receptor',[Receptores::class,'eliminar_receptor'])->name('eliminar_receptor');
Route::get('ver_editar_receptor',[Receptores::class,'ver_editar_receptor'])->name('ver_editar_receptor');
Route::get('cambiar_clave',[Receptores::class,'cambiar_clave'])->name('cambiar_clave');

Route::get('dar_acceso_a_docente',[Siseaprende2025::class,'dar_acceso_a_docente'])->name('dar_acceso_a_docente');
Route::get('tabla_reporte_nexus',[Siseaprende2025::class,'tabla_reporte_nexus'])->name('tabla_reporte_nexus');
Route::get('get_cantidad_alumnos_importados_x_seccion',[Siseaprende2025::class,'get_cantidad_alumnos_importados_x_seccion'])->name('get_cantidad_alumnos_importados_x_seccion');
Route::get('nivel_x_grado',[Siseaprende2025::class,'nivel_x_grado'])->name('nivel_x_grado');
Route::post('subir_archivo_siagie',[Siseaprende2025::class,'subir_archivo_siagie'])->name('subir_archivo_siagie');
Route::get('procesar_archivo_siagie',[Siseaprende2025::class,'procesar_archivo_siagie'])->name('procesar_archivo_siagie');
Route::get('eliminar_alumnos',[Siseaprende2025::class,'eliminar_alumnos'])->name('eliminar_alumnos');
Route::get('get_anadir_estudiantes',[Siseaprende2025::class,'get_anadir_estudiantes'])->name('get_anadir_estudiantes');
Route::POST('generar_enlace_nuevo_docente',[Siseaprende2025::class,'generar_enlace_nuevo_docente'])->name('generar_enlace_nuevo_docente');

Route::get('listar_PlantillaReporte',[PlantillaReporte::class,'listar_PlantillaReporte'])->name('listar_PlantillaReporte');
Route::get('tabla_PlantillaReporte',[PlantillaReporte::class,'tabla_PlantillaReporte'])->name('tabla_PlantillaReporte');
Route::post('guardar_PlantillaReporte',[PlantillaReporte::class,'guardar_PlantillaReporte'])->name('guardar_PlantillaReporte');
Route::get('eliminar_PlantillaReporte',[PlantillaReporte::class,'eliminar_PlantillaReporte'])->name('eliminar_PlantillaReporte');

Route::get('listar_PlantilaVariables',[PlantillaReporte::class,'listar_PlantilaVariables'])->name('listar_PlantilaVariables');
Route::get('tabla_PlantilaVariables',[PlantillaReporte::class,'tabla_PlantilaVariables'])->name('tabla_PlantilaVariables');
Route::post('guardar_PlantilaVariables',[PlantillaReporte::class,'guardar_PlantilaVariables'])->name('guardar_PlantilaVariables');
Route::get('verreportegenerado',[PlantillaReporte::class,'verreportegenerado'])->name('verreportegenerado');
Route::get('tabla_verreportegenerado',[PlantillaReporte::class,'tabla_verreportegenerado'])->name('tabla_verreportegenerado');
Route::get('pruebareporte',[PlantillaReporte::class,'pruebareporte'])->name('pruebareporte');

Route::get('listar_Plataforma',[Plataforma::class,'listar_Plataforma'])->name('listar_Plataforma');
Route::get('tabla_Plataforma',[Plataforma::class,'tabla_Plataforma'])->name('tabla_Plataforma');
Route::post('guardar_Plataforma',[Plataforma::class,'guardar_Plataforma'])->name('guardar_Plataforma');
Route::get('eliminar_Plataforma',[Plataforma::class,'eliminar_Plataforma'])->name('eliminar_Plataforma');

Route::get('contactanos',function(){
    $correo = new Notificarcorreo;
    $correo->subject = 'Todo';
    $correo->body = 'ds dsdsad asdasddadada dasdadasd';
    Mail::to('jacostaf@ugel01.gob.pe')->send($correo);
    return "Mensaje enviado";
});

Route::get('/', function () {
    return view('welcome');
});

/**inicio jmmj 28-01-2025 */
Route::get('bienes-usuario',[BienesUser::class,'index'])->name('bienes-usuario');
Route::get('bienes-transferidos',[BienesUser::class,'transferido'])->name('bienes-transferidos');
Route::post('refirma', [RefirmaInvokerController::class,"index"])->name("refirma");
Route::post('update-firmas', [RefirmaInvokerController::class,"update_firmas"])->name("update-firmas");
/**fin jmmj 28-01-2025 */

/**inicio jmmj 02-04-2025 */
Route::get('asistencia_teletrabajo',[AsistenciaTeletrabajo::class,'index'])->name('asistencia_teletrabajo');
/**fin jmmj 02-04-2025 */
Route::post('/registrar-asistencia',[AsistenciaTeletrabajo::class,'store'])->name('registrar-asistencia');
Route::post('/registrar-salida',[AsistenciaTeletrabajo::class,'salida'])->name('registrar-salida');
Route::get('/asistencia-teletrabajo',[AsistenciaTeletrabajo::class,'data'])->name('asistencia-teletrabajo');




//usuario
Route::get('/reporte-asistencia',[AsistenciaTeletrabajo::class,'reporte_asistencia'])->name('reporte-asistencia');
Route::get('/reporte-asistencia-teletrabajo',[AsistenciaTeletrabajo::class,'data_reporte_asistencia_teletrabajo'])->name('reporte-asistencia-teletrabajo');
//coordinador o jefe
Route::get('/monitoreo-asistencia',[AsistenciaTeletrabajo::class,'monitoreo_asistencia'])->name('monitoreo-asistencia');
Route::get('/data-monitoreo-asistencia',[AsistenciaTeletrabajo::class,'data_monitoreo_asistencia'])->name('data-monitoreo-asistencia');
//LINK ACCCESO COORDINADOR O JEFE
Route::post('/link-acceso',[AsistenciaTeletrabajo::class,'link_acceso'])->name('link-acceso');
//actividades en teletrabajo asignados
Route::get('/actividades-teletrabajo',[AsistenciaTeletrabajo::class,'actividades_teletrabajo'])->name('actividades-teletrabajo');
Route::post('/guardar-actividades',[AsistenciaTeletrabajo::class,'guardar_actividades'])->name('guardar-actividades');
Route::get('/data-actividad-teletrabajo',[AsistenciaTeletrabajo::class,'data_actividad_teletrabajo'])->name('data-actividad-teletrabajo');

//actividad teletrabajo usuario respuesta
Route::get('/actividades-teletrabajo-respuesta',[AsistenciaTeletrabajo::class,'actividades_teletrabajo_respuesta'])->name('actividades-teletrabajo-respuesta');
Route::post('/guardar-actividades-respuesta',[AsistenciaTeletrabajo::class,'guardar_actividades_respuesta'])->name('guardar-actividades-respuesta');
Route::get('/data-actividad-teletrabajo-respuesta',[AsistenciaTeletrabajo::class,'data_actividad_teletrabajo_respuesta'])->name('data-actividad-teletrabajo-respuesta');

Route::get('/data-monitoreo-asistencia1',[AsistenciaTeletrabajo::class,'data_monitoreo_asistencia1'])->name('data-monitoreo-asistencia1');

//jmmj 21-05-2025 inicio
Route::post('refirma-no-patrimonial', [RefirmaInvokerNoPatrimonialController::class,"index"])->name("refirma-no-patrimonial");
Route::post('update-firmas-no-patrimonial', [RefirmaInvokerNoPatrimonialController::class,"update_firmas"])->name("update-firmas-no-patrimonial");
Route::get('bienes-transferidos-no-patrimonial',[BienesUserNoPatrimonial::class,'transferido'])->name('bienes-transferidos-no-patrimonial');

//jmmj 21-05-2025 fin



//jmmj 30-05-2025 inicio
Route::post('guardar-cumplimiento',[AsistenciaTeletrabajo::class,'cumplimiento'])->name('guardar-cumplimiento');

Route::get('/reporte-actividades-teletrabajo-pdf/{fecha_inicio}/{fecha_fin}', [AsistenciaTeletrabajo::class, 'exportarActividadesPDF'])->name('reporte.actividades.pdf');
Route::get('/reporte-actividades-teletrabajo-pdf1/{fecha_inicio}/{fecha_fin}/{salto_linea}', [AsistenciaTeletrabajo::class, 'exportarActividadesPDF1'])->name('reporte.actividades.pdf1');
//jmmj 30-05-2025 fin

Route::post('guardar-observacion',[AsistenciaTeletrabajo::class,'guardar_observacion'])->name('guardar-observacion');

//Anexo 03 - 13-06-2025
Route::get('/reporte_anexo03', [Anexo03Controller::class, 'mostrarAsistenciaDetallada']);
Route::post('/guardar-reporte-masivo', [Anexo03Controller::class, 'guardarReporteMasivo'])->name('guardar.reporte.masivo');
Route::POST('/asistencia/pdf', [Anexo03Controller::class, 'exportarAsistenciaPDF'])->name('asistencia.exportar.pdf');
Route::post('/guardar-firma-director', [Anexo03Controller::class, 'guardarFirma'])->name('guardar.firma.director');
//Anexo 04 - 13-06-2025
Route::get('/reporte_anexo04', [Anexo04Controller::class, 'mostrarInasistenciaDetallada']);
Route::post('/guardar-reporte-consolidado-masivo', [Anexo04Controller::class, 'storeMasivo'])->name('anexo04.storeMasivo');
Route::post('/guardar-firma-director', [Anexo04Controller::class, 'guardarFirma'])->name('guardar.firma.director');
Route::POST('/inasistencia/pdf', [Anexo04Controller::class, 'exportarInasistenciaPDF'])->name('inasistencia.exportar.pdf');
//Vista ESP - ANEXOS - 13-06-2025
Route::get('/reportes_anexo03',[ReporteAnexosController::class,'mostrarReporteAnexos03'])->name('reporte.anexos03');
Route::get('/reportes_anexo04',[ReporteAnexosController::class,'mostrarReporteAnexos04'])->name('reporte.anexos04');




