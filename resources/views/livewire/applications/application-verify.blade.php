<div>
    <div class="card">
        <div class="card-header">
            <h3>Verify Application</h3>
        </div>
        <div class="card-body">

              <form action=""
                    method="POST" enctype="multipart/form-data"   wire:submit.prevent='verifyApplication'>
                  
                @csrf
                @method('PUT')
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="school_name">School Name </label>
                    <input type="text" class="form-control" id="school_name"
                           wire:model="school_name" readonly>
                    @error('school_name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group col-md-4">
                    <label for="registration_number">Registration Number </label>
                    <input type="text" class="form-control" id="registration_number"
                           wire:model="registration_number"
                           value="">
                    @error('registration_number')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>

                  <div class="form-group col-md-4">
                    <label for="contract_number">Contract Number </label>
                    <input type="text" class="form-control" id="contract_number" wire:model="contract_number" placeholder=" eg  ABC/DEF/GHI/1234/2025"
                           value="">
                    @error('contract_number')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
            </div>
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="color">Color (Optional) </label>
                    <input type="color" class="form-control" id="color" wire:model="color"
                           value="" >
                    @error('color')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                  </div>
                  <div class="form-group col-lg-6 col-md-6">
                      <label for="phone"> Phone Number (Optional)</label>
                      <input type="text" class="form-control" id="phone" placeholder="07XXXXXXXXXX"
                          wire:model="phone" value="{{ old('phone') }}">


                  </div>
            </div>
        
                <div class="form-group">
                  <label for="motto">Motto </label>
                  <input type="text" class="form-control" id="motto" wire:model="motto"
                         value="">
                  @error('motto')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
                </div>
        
                <div class="form-group">
                  <label for="address">Address (Optional)</label>
                  <textarea id="address" wire:model="address" class="form-control">{{ old('address') }}</textarea>
                </div>
                @error('address')
                <div class="text-danger">{{ $message }}</div>
                @enderror
        
               
        
                <div class="form-group">
                  <label for="logo">Logo </label>
                  <input type="file" class="form-control-file" id="logo" wire:model="logo" accept="image/*">

                  <span wire:loading wire:target="logo" class='py-2'>
                    <i class="fa fa-spinner fa-spin"></i>please waiting while its checking file...
    
                </span>
                  @error('logo')
                  <div class="text-danger">{{ $message }}</div>
                  @enderror
                </div>
                <button type="submit" class="btn btn-primary" wire:loading.attr='hidden'>
                    Verify 
                </button>
                <span wire:loading wire:target="verifyApplication">
                    <i class="fa fa-spinner fa-spin"></i>

                </span>

                <a href="{{ route('application.list')}}" class="btn btn-secondary">Cancel</a>
              </form>
          </div>
    </div>
</div>
