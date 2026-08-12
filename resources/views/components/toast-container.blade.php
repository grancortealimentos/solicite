{{-- resources/views/components/toast-container.blade.php --}}
{{--
Fica no layout, fora do fluxo do documento.
pointer-events-none no container + pointer-events-auto nos toasts:
permite clicar "através" da área vazia da coluna de toasts.
--}}
<div class="fixed top-5 end-5 z-[100] flex flex-col gap-y-2 pointer-events-none">
    @if (session('status'))
        <x-toast type="success">{{ session('status') }}</x-toast>
    @endif

    @if (session('error'))
        <x-toast type="error">{{ session('error') }}</x-toast>
    @endif

    @if (session('warning'))
        <x-toast type="warning">{{ session('warning') }}</x-toast>
    @endif

    @if (session('info'))
        <x-toast type="info">{{ session('info') }}</x-toast>
    @endif
</div>