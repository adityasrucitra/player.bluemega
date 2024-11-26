<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MP3 Files Table</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">MP3 Files</h2>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">File Name</th>
                <th scope="col">Playback</th>
            </tr>
        </thead>
        <tbody>
            <!-- Repeat this block for each MP3 file -->
            <tr>
                <th scope="row">1</th>
                <td>example1.mp3</td>
                <td>
                    <audio controls>
                        <source src="http://player.bluemega.com/recorded/sample-12s.mp3" type="audio/mp3">
                        Your browser does not support the audio element.
                    </audio>
                </td>
            </tr>
            <tr>
                <th scope="row">1</th>
                <td>example1.mp3</td>
                <td>
                    <audio controls>
                        <source src="http://player.bluemega.com/recorded/sample-12s.mp3" type="audio/mp3">
                        Your browser does not support the audio element.
                    </audio>
                </td>
            </tr>
            <!-- Repeat block ends -->
        </tbody>
    </table>
</div>

<!-- Bootstrap JS and dependencies (optional) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>

</script>
</body>
</html>
