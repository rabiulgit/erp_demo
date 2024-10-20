<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropdown Menu</title>
</head>
<body>
    <h1>Select an Option</h1>
    <form>
        <select name="dropdown">
            <option value="">Select...</option>
            @foreach ($dropdownData as $item)
            <option value="{{ $item['autoAcNo'] }}">{{ $item['udAcName'] }}</option>
            @endforeach
        </select>
    </form>
</body>
</html>
