<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# 🧠 BPM System - Business Process Management

Este proyecto es un sistema BPM (Business Process Management) desarrollado con Laravel. Permite importar procesos definidos en archivos **XPDL**, ejecutar flujos de trabajo, y gestionar actividades de usuario.

📦 Repositorio: [JoeTancara/bpm-system](https://github.com/JoeTancara/bpm-system)

---

## ✅ Requisitos Previos

- PHP >= 8.x
  
- Composer

- MySQL o MariaDB
  
- Node.js y npm (opcional, para frontend o compilación de assets)
  
- [XAMPP](https://www.apachefriends.org/es/index.html) (opcional, para entorno local)

---

## ⚙️ Instalación

1. **Clona el repositorio:**

   ```bash
   
   git clone https://github.com/JoeTancara/bpm-system.git
   
   cd bpm-system
   
## Instala dependencias PHP:

composer install

## Configura el entorno:

cp .env.example .env

## Pasos para Usar el Sistema

🛠️ Migrar la base de datos

php artisan migrate

## 📥 Importar procesos XPDL
Asegúrate de tener los archivos .xpdl en storage/app/xpdl/, luego ejecuta:

php artisan import:xpdl storage/app/xpdl/proceso_3.xpdl

php artisan import:xpdl storage/app/xpdl/proceso_4.xpdl

php artisan import:xpdl storage/app/xpdl/proceso_5.xpdl

php artisan import:xpdl storage/app/xpdl/proceso_6.xpdl

php artisan import:xpdl storage/app/xpdl/proceso_7.xpdl

php artisan import:xpdl storage/app/xpdl/nuevo-silva.xpdl


## ⚙️ Activar el modo "decisión" en actividades de usuario

Abre Tinker:

php artisan tinker

\App\Models\Activity::where('type','userTask')->get()->each(function($act){

    $cfg = is_array($act->config) ? $act->config : [];
    
    $cfg['decision'] = true;
    
    $act->config = $cfg;
    
    $act->save();
    
});

exit

## 👨‍💻 Autor

Desarrollado por Joe Tancara

