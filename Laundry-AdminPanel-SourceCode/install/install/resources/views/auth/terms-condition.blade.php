<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($websiteName[0]) ? $websiteName[0]->name : '' }} {{ __('terms_conditions_page_title') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 2rem;
        }

        .privacy-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 2rem;
        }

        .section-icon {
            font-size: 1.5rem;
            margin-right: 0.5rem;
            color: #3b82f6;
            /* blue icon color */
        }

        h1,
        h2 {
            font-weight: bold;
        }

        a {
            color: #3b82f6;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="container privacy-container">
        <h1 class="text-center mb-3">{{ isset($websiteName[0]) ? $websiteName[0]->name : '' }}
            {{ __('terms_conditions_page_title') }}</h1>

        <p class="text-center">{{ __('terms_conditions_intro') }}</p>

        <hr>

        <div class="mb-4">
            <h4><span
                    class="section-icon">📜</span>{{ __($setting->value['title'] ?? __('terms_conditions_default_title')) }}
            </h4>
            <p>{!! $setting->value['content'] ?? __('no_content_added') !!}</p>
        </div>


    </div>

</body>

</html>
