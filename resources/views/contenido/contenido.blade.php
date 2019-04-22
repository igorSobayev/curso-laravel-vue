{{-- Decimos que este archivo estará incluido en principal y que el código de abajo será la extensión del principal --}}
@extends('principal')
{{-- Creamos el contenido a insertar en nuestra página principal --}}
@section('contenido')

    {{-- Control de quien accede a la aplicación y lo que le ofrecemos --}}
    @if (Auth::check())
        {{-- Admin --}}
        @if (Auth::user() -> idrol == 1)
            <template v-if="menu==0">
                <h1>Escritorio</h1>
            </template>
        
            <template v-if="menu==1">
                <categoria></categoria>
            </template>
        
            <template v-if="menu==2">
                <articulo></articulo>
            </template>
        
            <template v-if="menu==3">
                <ingreso></ingreso>
            </template>
        
            <template v-if="menu==4">
                <proveedor></proveedor>
            </template>
        
            <template v-if="menu==5">
                <venta></venta>
            </template>
        
            <template v-if="menu==6">
                <cliente></cliente>
            </template>
        
            <template v-if="menu==7">
                <user></user>
            </template>
        
            <template v-if="menu==8">
                <rol></rol>
            </template>
        
            <template v-if="menu==9">
                <h1>Reporte de ingresos</h1>
            </template>
        
            <template v-if="menu==10">
                <h1>Reporte de ventas</h1>
            </template>
        
            <template v-if="menu==11">
                <h1>Ayuda</h1>
            </template>
        
            <template v-if="menu==12">
                <h1>Acerda de ...</h1>
            </template>
            {{-- Vendedor --}}
        @elseif (Auth::user() -> idrol == 2)
            <template v-if="menu==5">
                <venta></venta>
            </template>
        
            <template v-if="menu==6">
                <cliente></cliente>
            </template>
            <template v-if="menu==10">
                <h1>Reporte de ventas</h1>
            </template>
            
            <template v-if="menu==11">
                <h1>Ayuda</h1>
            </template>
            
            <template v-if="menu==12">
                <h1>Acerda de ...</h1>
            </template>
            {{-- Almacenero --}}
        @elseif (Auth::user() -> idrol == 3)
            <template v-if="menu==1">
                <categoria></categoria>
            </template>
        
            <template v-if="menu==2">
                <articulo></articulo>
            </template>
        
            <template v-if="menu==3">
                <ingreso></ingreso>
            </template>
        
            <template v-if="menu==4">
                <proveedor></proveedor>
            </template>

            <template v-if="menu==11">
                <h1>Ayuda</h1>
            </template>
            
            <template v-if="menu==12">
                <h1>Acerda de ...</h1>
            </template>
        @else 

        @endif
    @endif
{{-- 
    <template v-if="menu==0">
        <h1>Escritorio</h1>
    </template>

    <template v-if="menu==1">
        <categoria></categoria>
    </template>

    <template v-if="menu==2">
        <articulo></articulo>
    </template>

    <template v-if="menu==3">
        <h1>Ingresos</h1>
    </template>

    <template v-if="menu==4">
        <proveedor></proveedor>
    </template>

    <template v-if="menu==5">
        <h1>Ventas</h1>
    </template>

    <template v-if="menu==6">
        <cliente></cliente>
    </template>

    <template v-if="menu==7">
        <user></user>
    </template>

    <template v-if="menu==8">
        <rol></rol>
    </template>

    <template v-if="menu==9">
        <h1>Reporte de ingresos</h1>
    </template>

    <template v-if="menu==10">
        <h1>Reporte de ventas</h1>
    </template>

    <template v-if="menu==11">
        <h1>Ayuda</h1>
    </template>

    <template v-if="menu==12">
        <h1>Acerda de ...</h1>
    </template>
     --}}
    
@endsection