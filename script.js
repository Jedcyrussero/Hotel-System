/**
 * Hotel System - Frontend JavaScript
 * 
 * - Fetches employees from the API and populates the dropdown
 * - Handles form submission to create a new booking
 */

document.addEventListener('DOMContentLoaded', function () {
    // ---- DOM References ----
    const employeeSelect = document.getElementById('employee_id');
    const bookingForm = document.getElementById('bookingForm');
    const submitBtn = document.getElementById('submitBtn');
    const resultDiv = document.getElementById('result');

    // ---- Load Employees into Dropdown ----
    async function loadEmployees() {
        try {
            // Show loading state
            employeeSelect.innerHTML = '<option value="">Loading employees...</option>';
            employeeSelect.disabled = true;

            const response = await fetch('api/get_employees.php');
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to fetch employees');
            }

            // Clear and populate dropdown
            employeeSelect.innerHTML = '<option value="">-- Select Employee --</option>';

            if (data.count === 0) {
                employeeSelect.innerHTML = '<option value="">No employees available</option>';
                return;
            }

            data.data.forEach(function (employee) {
                const option = document.createElement('option');
                option.value = employee.id;
                option.textContent = employee.name + ' (' + employee.position + ')';
                employeeSelect.appendChild(option);
            });

            employeeSelect.disabled = false;
        } catch (error) {
            console.error('Error loading employees:', error);
            employeeSelect.innerHTML = '<option value="">Error loading employees</option>';
            showResult('Failed to load employees. Please refresh the page.', 'error');
        }
    }

    // ---- Show Result Message ----
    function showResult(message, type) {
        resultDiv.textContent = message;
        resultDiv.className = 'result ' + type;
    }

    // ---- Handle Form Submission ----
    bookingForm.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Validate employee selection
        if (!employeeSelect.value) {
            showResult('Please select an employee from the dropdown.', 'error');
            employeeSelect.focus();
            return;
        }

        // Collect form data
        const formData = {
            employee_id: parseInt(employeeSelect.value),
            guest_name: document.getElementById('guest_name').value.trim(),
            room_number: document.getElementById('room_number').value.trim(),
            check_in: document.getElementById('check_in').value,
            check_out: document.getElementById('check_out').value,
            status: document.getElementById('status').value
        };

        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Creating Booking...';
        showResult('', '');

        try {
            const response = await fetch('api/create_booking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (data.success) {
                showResult(
                    ' ' + data.message +
                    '\n Guest: ' + data.data.guest_name +
                    '\n Room: ' + data.data.room_number +
                    '\n Check-in: ' + data.data.check_in +
                    '\n Check-out: ' + data.data.check_out +
                    '\n Status: ' + data.data.status,
                    'success'
                );
                bookingForm.reset();
            } else {
                showResult('❌ ' + data.message, 'error');
            }
        } catch (error) {
            console.error('Error creating booking:', error);
            showResult('❌ Network error. Please try again.', 'error');
        } finally {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Create Booking';
        }
    });

    // ---- Initialize ----
    loadEmployees();
});

