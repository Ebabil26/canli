<!DOCTYPE html>
<html>
<head>
    <title>Translation</title>
</head>
<body>
<form method="post" action="{{ route('translate') }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <label for="source">Source Language:</label>
    <input type="text" name="source" id="source" required><br><br>
    <label for="target">Target Language:</label>
    <input type="text" name="target" id="target" required><br><br>
    <label for="q">Word:</label>
    <input type="text" name="q" id="q" required><br><br>
    <button type="submit">Translate</button>
</form>
</body>
</html>
