<?php
/**
 * @var array $meta
 * @var array $sections
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $meta['title'] ?></title>
</head>
<body>
<?php
if (isset($sections['content'])) {
    echo $sections['content'];
}
?>
<?php
dump(get_defined_vars());
?>



<button id="clickTest">test</button>
<script>
    document.getElementById('clickTest').addEventListener('click', function () {
        fetch('http://localhost:8800/?news')
            .then(response => {
                return response.text();
            })
            .then(data => {
                console.log(data);
            });
    });
</script>

<button id="clickTest2">test2</button>
<script>
    document.getElementById('clickTest2').addEventListener('click', function () {
        fetch('http://localhost:8800/?news', {
            headers: {
                'Accept': 'application/json',
            }
        })
            .then(response => {
                return response.text();
            })
            .then(data => {
                console.log(data);
            });
    });
</script>

</body>
</html>
