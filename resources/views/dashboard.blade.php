<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Hola {{ Auth::user()->name }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script>

    $(document).ready(function() {
        $.ajax({
            url: 'https://roma.pompeyo.cl/respaldo/htmlv1/loginLaravel.php',
            type: 'POST',
                data: {
                    email: 'cristian.fuentealba@pompeyo.cl',
                    pass: 'ne0l0gik'
                },
            success: function(data) {
                console.log(data);
            }
        });
    });
</script>
