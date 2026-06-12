@include('clients.blocks.header')
@include('clients.blocks.banner')

<section class="container" style="margin-top:50px; margin-bottom:100px">

    <h1 class="mb-4">Lịch khám của tôi</h1>

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            <i class="fa fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if ($appointments->isEmpty())
        <div class="text-center py-5">
            <i class="fa fa-calendar-times" style="font-size:48px; color:#ccc;"></i>
            <p class="text-muted mt-3">Bạn chưa có lịch hẹn nào.</p>
            <a href="{{ route('appointment.create') }}" class="btn btn-primary">
                <i class="fa fa-calendar-plus"></i> Đặt lịch khám ngay
            </a>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Bác sĩ</th>
                        <th>Chuyên khoa</th>
                        <th>Ngày khám</th>
                        <th>Giờ khám</th>
                        <th>Triệu chứng</th>
                        <th>Phí khám</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($appointments as $index => $appointment)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $appointment->doctor->name ?? '—' }}</td>
                            <td>{{ $appointment->doctor->specialty->name ?? '—' }}</td>
                            <td>{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d/m/Y') }}</td>
                            <td>{{ substr($appointment->appointment_time, 0, 5) }}</td>
                            <td>{{ $appointment->symptoms ?? '—' }}</td>
                            <td>{{ number_format($appointment->fee) }}đ</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $appointment->payment_method === 'momo' ? 'MoMo' : 'Tiền mặt' }}
                                </span>
                            </td>
                            <td>
                                @switch($appointment->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge bg-primary">Đã xác nhận</span>
                                        @break
                                    @case('completed')
                                        <span class="badge bg-success">Hoàn thành</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger">Đã hủy</span>
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $appointment->status }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if (in_array($appointment->status, ['pending', 'confirmed']))
                                    <form action="{{ route('appointment.cancel', $appointment->id) }}" method="POST"
                                          onsubmit="return confirm('Bạn có chắc muốn hủy lịch hẹn này?');" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa fa-times"></i> Hủy
                                        </button>
                                    </form>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</section>

@include('clients.blocks.footer')