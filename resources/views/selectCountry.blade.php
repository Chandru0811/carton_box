<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Select Country</title>
</head>

<style>
    .selected_screen {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap; /* Allows items to wrap on smaller screens */
    gap: 10px; /* Adds spacing between items */
    justify-content: center; /* Centers items in the container */
}

.cb_screens {
    background: #f8f9fa; /* Light background */
    border-radius: 8px;
    padding: 8px 12px;
    transition: background 0.3s ease, transform 0.2s ease;
}

.cb_screens:hover {
    background: #e2e6ea; /* Slightly darker on hover */
    transform: translateY(-2px);
}

.screen_list {
    display: flex;
    align-items: center;
    gap: 8px; /* Space between flag and text */
    text-decoration: none;
    color: #333;
    font-weight: 500;
    white-space: nowrap; /* Prevents text from wrapping */
}

.screen_list img {
    border-radius: 3px;
    object-fit: cover;
}

/* Responsive Styling */
@media (max-width: 768px) {
    .selected_screen {
        flex-direction: column;
        align-items: center;
    }

    .cb_screens {
        width: 100%;
        text-align: center;
    }

    .screen_list {
        justify-content: center;
    }
}

</style>

<body>
    <section class="">
        <ul class="selected_screen my-5 py-5">
            @foreach ($countries as $country)
                <li class="cb_screens">
                    <a class="screen_list" href="{{ route('set.country', ['country_code' => $country->country_code]) }}">
                        <img src="{{ $country->flag }}" alt="{{ $country->country_name }}" width="20">
                        {{ $country->country_name }}
                    </a>
                </li>
            @endforeach
        </ul>

    </section>
</body>

</html>
