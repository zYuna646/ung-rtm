@props([
'color' => 'primary',
'class' => '',
'type' => 'button',
'size' => 'md',
'outlined' => false,
])

@php
// Define size classes
$sizeClasses = [
'sm' => 'px-3.5 py-2 text-xs',
'md' => 'px-5 py-2.5 text-sm',
'lg' => 'px-5 py-3 text-base',
];

// Define color classes
$colorClasses = [
'primary' => [
'bg' => 'bg-color-primary-500',
'border' => 'border-color-primary-500',
'text' => 'text-white',
'hover' => 'hover:bg-color-primary-400',
'outlined_bg' => 'bg-white',
'outlined_text' => 'text-color-primary-500',
'outlined_hover' => 'hover:bg-color-primary-500 hover:text-white',
],
'secondary' => [
'bg' => 'bg-gray-500',
'border' => 'border-gray-500',
'text' => 'text-white',
'hover' => 'hover:bg-gray-400',
'outlined_bg' => 'bg-white',
'outlined_text' => 'text-gray-500',
'outlined_hover' => 'hover:bg-gray-500 hover:text-white',
],
'success' => [
'bg' => 'bg-color-success-500',
'border' => 'border-color-success-500',
'text' => 'text-white',
'hover' => 'hover:bg-color-success-400',
'outlined_bg' => 'bg-white',
'outlined_text' => 'text-color-success-500',
'outlined_hover' => 'hover:bg-color-success-500 hover:text-white',
],
'warning' => [
'bg' => 'bg-color-warning-500',
'border' => 'border-color-warning-500',
'text' => 'text-white',
'hover' => 'hover:bg-color-warning-400',
'outlined_bg' => 'bg-white',
'outlined_text' => 'text-color-warning-500',
'outlined_hover' => 'hover:bg-color-warning-500 hover:text-white',
],
'info' => [
'bg' => 'bg-color-info-500',
'border' => 'border-color-info-500',
'text' => 'text-white',
'hover' => 'hover:bg-color-info-400',
'outlined_bg' => 'bg-white',
'outlined_text' => 'text-color-info-500',
'outlined_hover' => 'hover:bg-color-info-500 hover:text-white',
],
'danger' => [
'bg' => 'bg-color-danger-500',
'border' => 'border-color-danger-500',
'text' => 'text-white',
'hover' => 'hover:bg-color-danger-400',
'outlined_bg' => 'bg-white',
'outlined_text' => 'text-color-danger-500',
'outlined_hover' => 'hover:bg-color-danger-500 hover:text-white',
],
'default' => [
'bg' => 'bg-white border',
'border' => 'border-neutral-500',
'text' => 'text-neutral-800',
'hover' => 'hover:bg-neutral-100',
],
// Add other color schemes as needed
];

// Determine if button is outlined or filled
$isOutlined = $outlined ? 'border ' . $colorClasses[$color]['border'] . ' ' . $colorClasses[$color]['outlined_bg'] . ' '
. $colorClasses[$color]['outlined_text'] . ' ' . $colorClasses[$color]['outlined_hover'] : $colorClasses[$color]['bg'] .
' ' . $colorClasses[$color]['text'] . ' ' . $colorClasses[$color]['hover'];

// Combine classes
$classes = $sizeClasses[$size] . ' font-medium rounded-lg transition-colors ' . $isOutlined . ' ' . $class;
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>