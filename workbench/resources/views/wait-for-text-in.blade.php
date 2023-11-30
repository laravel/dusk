<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <script type="text/javascript">
            function copy(e) {
                e.innerHTML = 'Copied!'

                setTimeout(() => {
                    e.innerHTML = 'Copy'
                }, 2000);
            }
        </script>
    </head>
    <body>
        <button dusk="copy-button" onclick="copy(this)">Copy</button>
    </body>
</html>
