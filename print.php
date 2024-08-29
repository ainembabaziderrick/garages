<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Preview Example</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function openPrintPreview(url, redirectUrl) {
            var printWindow = window.open(url, '_blank');

            // Set up the onload event to trigger the print dialog
            printWindow.onload = function() {
                printWindow.print();
            };

            // Listen for the afterprint event to redirect
            printWindow.onafterprint = function() {
                printWindow.close(); // Close the print window
                // Redirect the parent window
                window.location.href = redirectUrl; 
                // history.go(-1)
            };
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <a href="#" class="btn btn-primary" onclick="openPrintPreview('receipt.php')">Print Receipt</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
