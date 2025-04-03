<form action="{{ route('mitarbeiter.logout') }}" method="POST">
    @csrf
    <button type="submit" class="btn dropdown-item"><i class="bx bx-log-out"></i> Logout</button>
</form>