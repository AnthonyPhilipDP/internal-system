<!-- filepath: resources/views/livewire/worksheet.blade.php -->
<div class="bg-[iamge:url('sampleTemplate.jpg')]">
    <h1 class="uppercase">User List</h1>
    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Name</th>
                <th class="py-2 px-4 border-b">Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $user->name }}</td>
                    <td class="py-2 px-4 border-b">{{ $user->email }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <h1 class="uppercase mt-8 mb-4">Equipment List</h1>
    <table class="min-w-full bg-white border border-gray-200">
        <thead>
            <tr>
                <th class="py-2 px-4 border-b">Manufacturer</th>
                <th class="py-2 px-4 border-b">Model</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipment as $item)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $item->manufacturer }}</td>
                    <td class="py-2 px-4 border-b">{{ $item->model }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>