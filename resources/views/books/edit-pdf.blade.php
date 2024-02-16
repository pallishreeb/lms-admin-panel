<!-- pdf-editor.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sohojpora  - PDF Editor</title>
    <!-- Add CSS styles -->
    <style>
        .App {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            }

            .pdf-container {
            border: 1px solid #ccc;
            width: 80%;
            max-width: 800px;
            margin-bottom: 20px;
            }

            .controls {
            display: flex;
            justify-content: center;
            }

            .controls button {
            margin: 0 10px;
            }

            .header {
            margin-bottom: 20px;
            }

            .input-field {
            margin-right: 10px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            }

            .add-button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background-color: #ca7735;
            color: #fff;
            cursor: pointer;
            }

            .pdf-container {
            margin-bottom: 20px;
            }

            .controls {
            margin-bottom: 20px;
            }

            .page-nav-button {
            margin-right: 10px;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background-color: #ca7735;
            color: #fff;
            cursor: pointer;
            }

            .page-info {
            margin-bottom: 20px;
            }
            .page-number-input {
            margin-left: 10px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            }

            .annotations{
            
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .save{
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            background-color: #ca7735;
            color: #fff;
            cursor: pointer;
            }
    </style>
    @vitereactrefresh
    @vite(['resources/js/app.js'])
</head>
<body>
<div id="app"></div>
<script src="{{ asset('build/assets/app-T5TJOddm.js') }}"></script>

</body>
</html>
