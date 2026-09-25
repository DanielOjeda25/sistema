<?php

/*
 |----------------------------------------------------------------------
 | ACCESOS POR ROL — la fuente unica de verdad de la UI
 |----------------------------------------------------------------------
 | Todo lo que depende del rol vive aca: que enlaces del sidebar ve cada
 | rol y que acciones puede ejecutar en cada modulo. Las vistas dejan de
 | tener @hasanyrole sueltos y consultan esta configuracion, asi cambiar
 | un permiso es editar esta linea y no tocar 20 archivos.
 |
 | roles de menu: lista de roles que ven el enlace. "*" = todos los roles
 | internos. El rol Cliente tiene su propio juego de enlaces.
 */

return [

    // ------------------------------------------------------------------
    // SIDEBAR: orden y visibilidad de los enlaces del menu lateral.
    // ------------------------------------------------------------------
    'menu' => [
        ['ruta' => 'dashboard',           'label' => 'Dashboard',           'icono' => 'heroicon-o-home',           'roles' => ['Jefe', 'PM', 'PO', 'Programador', 'Cliente'], 'label_cliente' => 'Dashboard'],
        ['ruta' => 'proyectos.index',     'label' => 'Proyectos',           'icono' => 'heroicon-o-squares-2x2',    'roles' => ['Jefe', 'PM', 'PO', 'Programador', 'Cliente'], 'label_cliente' => 'Mis proyectos'],
        ['ruta' => 'tareas.tablero',      'label' => 'Mi trabajo',          'icono' => 'heroicon-o-check-circle',   'roles' => ['Jefe', 'PM', 'PO', 'Programador']],
        ['ruta' => 'tareas.index',        'label' => 'Tareas',              'icono' => 'heroicon-o-queue-list',     'roles' => ['Jefe', 'PM', 'PO', 'Programador']],
        ['ruta' => 'facturas.index',      'label' => 'Facturas',            'icono' => 'heroicon-o-banknotes',      'roles' => ['Jefe', 'PM', 'PO', 'Programador', 'Cliente']],
        ['ruta' => 'clientes.index',      'label' => 'Clientes y empresas', 'icono' => 'heroicon-o-building-office-2', 'roles' => ['Jefe', 'PM', 'PO', 'Programador']],
        ['ruta' => 'sprints.index',       'label' => 'Sprints',             'icono' => null,                        'roles' => ['Jefe', 'PM', 'PO', 'Programador'], 'seccion' => 'Modulos'],
        ['ruta' => 'hitos.index',         'label' => 'Hitos',               'icono' => null,                        'roles' => ['Jefe', 'PM', 'PO', 'Programador'], 'seccion' => 'Modulos'],
        ['ruta' => 'solicitudes-cambio.index', 'label' => 'Cambios',        'icono' => null,                        'roles' => ['Jefe', 'PM', 'PO', 'Programador'], 'seccion' => 'Modulos'],
        ['ruta' => 'entregables.index',   'label' => 'Entregables',         'icono' => null,                        'roles' => ['Jefe', 'PM', 'PO', 'Programador', 'Cliente'], 'seccion' => 'Modulos'],
        ['ruta' => 'users.index',         'label' => 'Usuarios y roles',    'icono' => 'heroicon-o-users',          'roles' => ['Jefe']],
        ['ruta' => 'auditoria.index',     'label' => 'Auditoría',           'icono' => 'heroicon-o-clock',          'roles' => ['Jefe']],
    ],

    // ------------------------------------------------------------------
    // ACCIONES por modulo: quien puede hacer que. Las vistas y los
    // controllers consultan esta matriz (Acceso::puede('facturas', 'editar')).
    // 'ver' esta implicito para todo rol que accede al modulo.
    // ------------------------------------------------------------------
    'acciones' => [
        'clientes' => [
            'crear' => ['Jefe', 'PM'],
            'editar' => ['Jefe', 'PM'],
            'eliminar' => ['Jefe'],
        ],
        'proyectos' => [
            'crear' => ['Jefe', 'PM'],
            'editar' => ['Jefe', 'PM'],
            'eliminar' => ['Jefe'],
        ],
        'facturas' => [
            'crear' => ['Jefe', 'PM'],
            'editar' => ['Jefe', 'PM'],
            'eliminar' => ['Jefe', 'PM'],
        ],
        'tareas' => [
            'crear' => ['Jefe', 'PM', 'PO'],
            'editar' => ['Jefe', 'PM', 'PO'],
            'eliminar' => ['Jefe', 'PM', 'PO'],
        ],
        'hitos' => [
            'crear' => ['Jefe', 'PM', 'PO'],
            'editar' => ['Jefe', 'PM', 'PO'],
            'eliminar' => ['Jefe'],
        ],
        'solicitudes' => [
            'crear' => ['Jefe', 'PM', 'PO'],
            'editar' => ['Jefe', 'PM', 'PO'],
            'eliminar' => ['Jefe'],
        ],
        'sprints' => [
            'crear' => ['Jefe', 'PM', 'PO'],
            'editar' => ['Jefe', 'PM', 'PO'],
            'eliminar' => ['Jefe', 'PM', 'PO'],
        ],
        'entregables' => [
            'crear' => ['Jefe', 'PM', 'PO', 'Programador'],
            'editar' => ['Jefe', 'PM', 'PO', 'Programador'],
            'eliminar' => ['Jefe'],
        ],
        'clientes_usuarios' => [ // gestion de cuentas de usuario (rol Jefe)
            'crear' => ['Jefe'],
            'editar' => ['Jefe'],
            'eliminar' => ['Jefe'],
            'cambiar_rol' => ['Jefe'],
        ],
    ],
];
