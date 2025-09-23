<?php

namespace App\Traits;

trait CommonValidations
{
    /**
     * Common user validation rules
     */
    protected function getUserValidationRules($isUpdate = false)
    {
        $emailRule = $isUpdate ? 'email|unique:users,email,' . request()->route('user') : 'email|unique:users,email';

        return [
            'name' => 'required|string|max:255',
            'email' => "required|{$emailRule}",
            'telefono' => 'nullable|string|max:20',
            'password' => $isUpdate ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'rol' => 'required|in:administrador,lider,vendedor,cliente',
            'activo' => 'boolean'
        ];
    }

    /**
     * Common product validation rules
     */
    protected function getProductValidationRules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|exists:categorias,_id',
            'activo' => 'boolean',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ];
    }

    /**
     * Common order validation rules
     */
    protected function getOrderValidationRules()
    {
        return [
            'cliente_id' => 'required|exists:users,_id',
            'vendedor_id' => 'nullable|exists:users,_id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,_id',
            'productos.*.cantidad' => 'required|integer|min:1',
            'productos.*.precio_unitario' => 'required|numeric|min:0',
            'direccion_entrega' => 'required|string|max:500',
            'telefono_contacto' => 'required|string|max:20',
            'observaciones' => 'nullable|string|max:1000'
        ];
    }

    /**
     * Date range validation rules
     */
    protected function getDateRangeValidationRules()
    {
        return [
            'fecha_inicio' => 'nullable|date|before_or_equal:fecha_fin',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio|before_or_equal:today'
        ];
    }

    /**
     * Commission validation rules
     */
    protected function getComisionValidationRules()
    {
        return [
            'vendedor_id' => 'required|exists:users,_id',
            'monto' => 'required|numeric|min:0',
            'periodo' => 'required|string|max:50',
            'estado' => 'required|in:pendiente,pagada,cancelada',
            'observaciones' => 'nullable|string|max:500'
        ];
    }

    /**
     * Referral validation rules
     */
    protected function getReferidoValidationRules()
    {
        return [
            'referidor_id' => 'required|exists:users,_id',
            'referido_id' => 'required|exists:users,_id|different:referidor_id',
            'codigo_referido' => 'required|string|unique:referidos,codigo_referido',
            'bono_aplicado' => 'boolean',
            'observaciones' => 'nullable|string|max:500'
        ];
    }

    /**
     * Configuration validation rules
     */
    protected function getConfigValidationRules()
    {
        return [
            'clave' => 'required|string|max:100|unique:configuraciones,clave',
            'valor' => 'required|string|max:1000',
            'tipo' => 'required|in:texto,numero,booleano,porcentaje,fecha',
            'descripcion' => 'nullable|string|max:500',
            'categoria' => 'required|string|max:50'
        ];
    }

    /**
     * File upload validation rules
     */
    protected function getFileUploadValidationRules($maxSize = 2048)
    {
        return [
            'file' => "required|file|max:{$maxSize}",
            'type' => 'required|in:image,document,excel,pdf'
        ];
    }

    /**
     * Custom validation messages
     */
    protected function getValidationMessages()
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe ser un email válido.',
            'unique' => 'El :attribute ya está en uso.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'max' => 'El campo :attribute no puede tener más de :max caracteres.',
            'numeric' => 'El campo :attribute debe ser un número.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'boolean' => 'El campo :attribute debe ser verdadero o falso.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'before_or_equal' => 'El campo :attribute debe ser anterior o igual a :date.',
            'after_or_equal' => 'El campo :attribute debe ser posterior o igual a :date.',
            'different' => 'El campo :attribute debe ser diferente a :other.',
            'exists' => 'El :attribute seleccionado no es válido.',
            'in' => 'El :attribute seleccionado no es válido.',
            'array' => 'El campo :attribute debe ser un arreglo.',
            'file' => 'El campo :attribute debe ser un archivo.',
            'image' => 'El campo :attribute debe ser una imagen.',
            'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
            'confirmed' => 'La confirmación de :attribute no coincide.'
        ];
    }

    /**
     * Custom attribute names
     */
    protected function getAttributeNames()
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'password_confirmation' => 'confirmación de contraseña',
            'telefono' => 'teléfono',
            'rol' => 'rol',
            'activo' => 'activo',
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'precio' => 'precio',
            'categoria_id' => 'categoría',
            'imagen' => 'imagen',
            'cliente_id' => 'cliente',
            'vendedor_id' => 'vendedor',
            'productos' => 'productos',
            'cantidad' => 'cantidad',
            'precio_unitario' => 'precio unitario',
            'direccion_entrega' => 'dirección de entrega',
            'telefono_contacto' => 'teléfono de contacto',
            'observaciones' => 'observaciones',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'monto' => 'monto',
            'periodo' => 'período',
            'estado' => 'estado',
            'referidor_id' => 'referidor',
            'referido_id' => 'referido',
            'codigo_referido' => 'código de referido',
            'bono_aplicado' => 'bono aplicado',
            'clave' => 'clave',
            'valor' => 'valor',
            'tipo' => 'tipo',
            'categoria' => 'categoría',
            'file' => 'archivo'
        ];
    }

    /**
     * Validate and return validated data
     */
    protected function validateRequest($rules, $messages = null, $attributes = null)
    {
        return request()->validate(
            $rules,
            $messages ?? $this->getValidationMessages(),
            $attributes ?? $this->getAttributeNames()
        );
    }
}