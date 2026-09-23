<div class="mt-4">
    <h5>Select Items and Quantity</h5>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>
                        <input type="checkbox" name="items[{{ $item->id }}][id]" value="{{ $item->id }}">
                    </td>
                    <td>{{ $item->name }}</td>
                    <td>
                        <input type="number" name="items[{{ $item->id }}][qty]" class="form-control" value="1" min="1">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>