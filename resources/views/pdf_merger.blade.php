<!DOCTYPE html>
<html>

<head>
    <title>Upload and Merge PDF Files</title>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"
        integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <style>
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h2>Upload and Merge PDF Files</h2>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="/merge-pdf" id="merge-pdf-form" method="POST" enctype="multipart/form-data">
        @csrf

        <h3>Upload PDF Files:</h3>

        <button type="button" id="add-field">Add More Files</button>
        <br><br>
        <br>
        <!-- Input fields for merging order and slide numbers for each file -->
        <div class="form-container">
            <!-- Initial set of form fields -->
            <div class="file-detailss">
            <div class="file-details">
                <label for="pdf_file_0">File Name:</label>
                <input type="file" name="pdf_file[0]" id="pdf_file_0" class="pdf-file" accept=".pdf" required>

                <label for="merge_order_0">Merging Order:</label>
                <input type="text" class="merge-order-input" name="merge_order[]" id="merge_order_0" value="1">

                <label for="slide_numbers_0">Slide Numbers:</label>
                <input type="text" class="slide-numbers-input" name="slide_numbers[0]" id="slide_numbers_0" required>

                <button type="button" class="remove-field">Remove</button>

                        </div>
                        </div>
        </div>
        <br><br>
        <div id="slideCountResult"></div>
        <div class="file-details-template" style="display: none;">
            <br>
            <div class="file-details">
                <label for="pdf_file_0">File Name:</label>
                <input type="file" name="pdf_file[]" class="pdf-file" accept=".pdf" required>

                <label for="merge_order_0">Merging Order:</label>
                <input type="text" class="merge-order-input" name="merge_order[]">

                <label for="slide_numbers_0">Slide Numbers:</label>
                <input type="text" class="slide-numbers-input" name="slide_numbers[]" required>
                <button type="button" class="remove-field">Remove</button>

            </div>
        </div>

        <button type="submit">Upload and Merge</button>
    </form>

    <script>
        $(document).ready(function () {
            // Add a custom validation method for unique merge_order values
            $.validator.addMethod("uniqueMergeOrder", function(value, element) {
                // Get all existing merge_order values
                var mergeOrderValues = $('.file-details  .merge-order-input').map(function() {
                    return this.value.trim();
                }).get();

                // Count the number of times the current value appears
                var count = $.grep(mergeOrderValues, function(element) {
                    return element === value.trim();
                }).length;

                // Return true if the count is 1 (i.e., the value is unique)
                return count === 1;
            }, "Merging Order must be unique.");

            // Add a click event handler to elements with the .remove-field class
            // $(".remove-field").click(function() {
            $("#merge-pdf-form").on('click', '.remove-field', function () {
                // alert();
                // Remove the parent element with the .file-detailss class
                $(this).closest(".file-detailss").remove();
                $('.file-details .pdf-file').each(function(index, element) {
                    // alert(index);
                    $(this).attr('id', 'pdf-file_' + index).attr('name','pdf_file['+ index +']').attr('id', 'pdf_file_' + index);
                    $(this).next("label").attr("id", 'pdf_file_' + index+ '-error').attr("for", 'pdf_file_' + index);
                });
                $('.file-details .slide-numbers-input').each(function(index, element) {
                    $(this).attr('id', 'slide_numbers_' + index).attr('name','slide_numbers['+ index +']').attr('id', 'slide_numbers_' + index);
                    $(this).next("label").attr("id", 'slide_numbers_' + index+ '-error').attr("for", 'slide_numbers_' + index);
                });
                $("#merge-pdf-form").validate();
            });

            // Clone the template and append it when the "Add More Files" button is clicked
            $('#add-field').click(function () {
                var clonedField = $('.file-details-template').clone();
                clonedField.removeClass('file-details-template');
                clonedField.addClass('file-detailss');
                clonedField.css('display', 'block');
                $('.form-container').append(clonedField);

                // Increment the index and update IDs and names for the new elements
                var newIndex = $('.file-details').length - 1;
                clonedField.find('.pdf-file').attr('id', 'pdf_file_' + newIndex).attr('name', 'pdf_file[' +
                    newIndex + ']');
                clonedField.find('.merge-order-input').attr('id', 'merge_order_' + newIndex).attr('name',
                    'merge_order[]').attr('value',newIndex);
                clonedField.find('.slide-numbers-input').attr('id', 'slide_numbers_' + newIndex).attr(
                    'name', 'slide_numbers[' + newIndex + ']');
            });

            // Attach keyup event handler to the initial merge_order input fields
            $('.form-container').on('blur', '.merge-order-input', checkForDuplicates);

            // Function to check for duplicate merge_order values
            function checkForDuplicates() {
                var mergeOrderValues = {};
                var hasDuplicates = false;

                $('.file-details .merge-order-input').each(function() {
                    var value = $(this).val().trim();

                    if (value === '' || value === null) {
                        return true;
                    }

                    if (value in mergeOrderValues) {
                        hasDuplicates = true;
                        $(this).css('color', 'red');
                        return false;
                    }
                    $(this).css('color', 'black');
                    mergeOrderValues[value] = true;
                });

                if (hasDuplicates) {
                    // Handle duplicate values
                } else {
                    // Handle non-duplicate values
                }
            }

            // Initialize the form validation
            $("#merge-pdf-form").validate({
                rules: {
                    // Use class selector for dynamically generated fields
                    "pdf_file[]": {
                        required: true,
                        accept: "application/pdf",
                    },
                    "merge_order[]": {
                        required: true,
                        digits: true, // Assuming merge_order should be numeric
                        uniqueMergeOrder: true, // Use the custom rule
                    },
                    "slide_numbers[]": {
                        required: true,
                        digits: true, // Assuming slide_numbers should be numeric
                    },
                },
                messages: {
                    "pdf_file[]": {
                        required: "Please select a PDF file.",
                        accept: "Only PDF files are allowed.",
                    },
                    "merge_order[]": {
                        required: "Please enter a merging order.",
                        digits: "Merging order should be numeric.",
                        uniqueMergeOrder: "Merging Order must be unique."
                    },
                    "slide_numbers[]": {
                        required: "Please enter slide numbers.",
                        digits: "Slide numbers should be numeric.",
                    },
                },
            });
        });
    </script>
</body>
</html>
