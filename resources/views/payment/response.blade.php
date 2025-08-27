<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Response</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header text-center">
                        <h3>Payment Response</h3>
                    </div>
                    <div class="card-body">
                        @if (isset($decision))
                            @if ($decision == 'ACCEPT')
                                <div class="alert alert-success text-center">
                                    <i class="bi bi-check-circle-fill fs-1 text-success d-block mb-3"></i>
                                    <h4>Payment Successful!</h4>
                                    <p>Your payment has been processed successfully.</p>
                                    @if (isset($request) && $request->input('req_reference_number'))
                                        <p><strong>Reference Number:</strong>
                                            {{ $request->input('req_reference_number') }}</p>
                                    @endif
                                    @if (isset($request) && $request->input('req_amount'))
                                        <p><strong>Amount:</strong> ${{ $request->input('req_amount') }}</p>
                                    @endif
                                </div>
                            @elseif($decision == 'DECLINE')
                                <div class="alert alert-danger text-center">
                                    <i class="bi bi-x-circle-fill fs-1 text-danger d-block mb-3"></i>
                                    <h4>Payment Declined</h4>
                                    <p>Your payment was declined. Please try again with a different payment method.</p>
                                    @if (isset($message))
                                        <p><strong>Reason:</strong> {{ $message }}</p>
                                    @endif
                                    @if (isset($reasonCode))
                                        <p><strong>Reason Code:</strong> {{ $reasonCode }}</p>
                                    @endif
                                </div>
                            @elseif($decision == 'ERROR')
                                <div class="alert alert-warning text-center">
                                    <i class="bi bi-exclamation-triangle-fill fs-1 text-warning d-block mb-3"></i>
                                    <h4>Payment Error</h4>
                                    <p>There was an error processing your payment. Please try again later.</p>
                                    @if (isset($message))
                                        <p><strong>Error:</strong> {{ $message }}</p>
                                    @endif
                                    @if (isset($reasonCode))
                                        <p><strong>Error Code:</strong> {{ $reasonCode }}</p>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="bi bi-info-circle-fill fs-1 text-info d-block mb-3"></i>
                                    <h4>Payment Status: {{ $decision }}</h4>
                                    @if (isset($message))
                                        <p>{{ $message }}</p>
                                    @endif
                                </div>
                            @endif
                        @else
                            <div class="alert alert-secondary text-center">
                                <i class="bi bi-question-circle-fill fs-1 text-secondary d-block mb-3"></i>
                                <h4>Payment Response Received</h4>
                                <p>We have received a response from the payment processor.</p>
                            </div>
                        @endif

                        <!-- Debug Information (only in local environment) -->
                        @if (config('app.debug') && isset($request))
                            <div class="mt-4">
                                <h5>Debug Information:</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <pre class="mb-0">{{ json_encode($request->all(), JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="text-center mt-4">
                            <a href="{{ route('tasks.index') }}" class="btn btn-primary">
                                <i class="bi bi-house-fill me-2"></i>Return to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show SweetAlert based on payment status
        @if (isset($decision))
            @if ($decision == 'ACCEPT')
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Successful!',
                    text: 'Your payment has been processed successfully.',
                    confirmButtonColor: '#28a745'
                });
            @elseif ($decision == 'DECLINE')
                Swal.fire({
                    icon: 'error',
                    title: 'Payment Declined',
                    text: 'Your payment was declined. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            @elseif ($decision == 'ERROR')
                Swal.fire({
                    icon: 'warning',
                    title: 'Payment Error',
                    text: 'There was an error processing your payment.',
                    confirmButtonColor: '#ffc107'
                });
            @endif
        @endif
    </script>
</body>

</html>
