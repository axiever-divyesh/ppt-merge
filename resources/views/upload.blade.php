<!DOCTYPE html>
<html>
<head>
    <title>Upload and Merge PowerPoint Files</title>
    <script
  src="https://code.jquery.com/jquery-3.7.1.slim.min.js"
  integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8="
  crossorigin="anonymous"></script>
</head>
<body>
    <h2>Upload and Merge PowerPoint Files</h2>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="/upload" method="POST" enctype="multipart/form-data">
        @csrf

        <h3>Upload PowerPoint Files:</h3>

        <button type="button" id="add-field">Add More Fields</button>
        <br><br>
        <br>
        <!-- Input fields for merging order and slide numbers for each file -->
        <div class="form-container">
        <!-- Initial set of form fields -->
            <div class="file-details">
                <label for="merge_order">File Name:</label>
                <input type="file" name="pptx_files[]">

                <label for="merge_order">Merging Order:</label>
                <input type="text" class="merge-order-input" name="merge_order[]">

                <label for="slide_numbers">Slide Numbers:</label>
                <input type="text" name="slide_numbers[]">
            </div>
            
        </div>
        <br><br>
        <div id="slideCountResult"></div>
        <div class="file-details-template" style="display: none;">
            <br>
            <div class="file-details">
                <label for="merge_order">File Name:</label>
                <input type="file" name="pptx_files[]">
                
                <label for="merge_order">Merging Order:</label>
                <input type="text" class="merge-order-input" name="merge_order[]">
                
                <label for="slide_numbers">Slide Numbers:</label>
                <input type="text" name="slide_numbers[]">
            </div>
        </div>
        
        <button type="submit">Upload and Merge</button>
    </form>
    <script>
        $(document).ready(function () {
            // Clone the template and append it when the "Add More Fields" button is clicked
            $('#add-field').click(function () {
                // alert('Add More Fields');
                var clonedField = $('.file-details-template').clone();
                clonedField.removeClass('file-details-template');
                clonedField.addClass('file-details');
                clonedField.css('display', 'block'); // Show the cloned fields
                $('.form-container').append(clonedField);
            });
            // Attach keyup event handler to the initial merge_order input fields
           $('.form-container').on('keyup', '.merge-order-input', checkForDuplicates);


            // Function to check for duplicate merge_order values
            function checkForDuplicates() {
                var mergeOrderValues = {};
                var hasDuplicates = false;

                // Iterate through each merge_order input
                $('.merge-order-input').each(function () {
                    var value = $(this).val();

                    if (value in mergeOrderValues) {
                        // Duplicate value found
                        hasDuplicates = true;
                        return false; // Exit the loop
                    }
                    console.log(value);
                    mergeOrderValues[value] = true;
                });

                if (hasDuplicates) {
                    alert('Duplicate merge_order values found.');
                } else {
                    console.log('No duplicate merge_order values found.');
                }
            }

            $('#getSlideCount').click(function () {
                var pptxFile = $('#pptxFile')[0].files[0];

                if (!pptxFile) {
                    alert('Please select a PPTX file.');
                    return;
                }

                var formData = new FormData();
                formData.append('pptx_file', pptxFile);

                $.ajax({
                    url: 'get_slide_count.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (response) {
                        if (response.slideCount >= 0) {
                            $('#slideCountResult').text('Slide Count: ' + response.slideCount);
                        } else {
                            $('#slideCountResult').text('Error: Unable to retrieve slide count.');
                        }
                    },
                    error: function () {
                        $('#slideCountResult').text('Error: Unable to connect to the server.');
                    }
                });
            });
        });
    </script>
</body>
</html>
