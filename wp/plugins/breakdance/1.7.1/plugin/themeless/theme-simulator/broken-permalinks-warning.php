<!doctype html>
<html>

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel='stylesheet' href='<?php echo BREAKDANCE_PLUGIN_URL . "plugin/themeless/normalize.min.css"; ?>'>
</head>

<body>

  <style>
    body {
      background-color: black;
      color: white;
      font-family: sans-serif;
      font-size: 19px;
    }

    a {
      color: white;
    }

    .wrap {
      max-width: 600px;
      margin-top: 200px;
      margin-left: auto;
      margin-right: auto;
      display: flex;
      flex-direction: column;
    }

    li {
      margin-bottom: 12px;
    }
  </style>

  <div class='wrap'>
    <h2>Please resave your permalinks.</h2>
    <p>Breakdance encountered a 404 error while loading the document.</p>
    <p>Instructions to Fix:</p>
    <ol>
      <li> <a force-allow-clicks href="<?php echo admin_url('options-permalink.php'); ?>" target="_blank">
          Go to Settings &gt; Permalinks
        </a>
      </li>
      <li>Click <em>Save Changes</em> at the bottom.</li>
      <li>Return to this screen and refresh.</li>
    </ol>

  </div>
</body>

</html>
