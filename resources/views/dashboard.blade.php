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

    <div id="iframeroma"></div>

</x-app-layout>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script>

    $(document).ready(function() {
            $.ajax({
                url: 'https://roma.pompeyo.cl/respaldo/htmlv1/php/controller/controller.session.php',
                type: 'POST',
                data: {
                    session_email: 'cristian.fuentealba@pompeyo.cl',
                    session_pass: 'ne0l0gik',
                    action : 'sessionOpen'
                },
                success: function (data) {
                    $("#iframeroma").html(
                        '<iframe src="https://roma.pompeyo.cl/respaldo/htmlv1/Home.html" frameborder="0" width="100%" height="600"></iframe>'
                    );
                    console.log(data);
                }
            });
    });
</script>
