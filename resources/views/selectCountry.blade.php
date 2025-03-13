<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Select Country</title>
</head>

<body>
    <section>
        <ul>
            @foreach ($countries as $country)
                <li>
                    <a href="{{ route('set.country', ['country_code' => $country->country_code]) }}">
                        <img src="{{ $country->flag }}" alt="{{ $country->country_name }}" width="20">
                        {{ $country->country_name }}
                    </a>
                </li>
            @endforeach
        </ul>

    </section>
</body>

</html>
