@extends('layouts.app')   

@section('content')
  <style>
    @keyframes gradient {
      0% {
        background-position: 0% 50%;
      }
      50% {
        background-position: 100% 50%;
      }
      100% {
        background-position: 0% 50%;
      }
    }

    .dynamic-gradient {
      background: radial-gradient(circle, #d1f1e2, #deebfc, #fceed5, #daf7e1, #f6f8d8);
      background-size: 300% 300%;
      animation: gradient 15s linear(0 0%, 0 1.8%, 0.01 3.6%, 0.03 6.35%, 0.07 9.1%, 0.13 11.4%, 0.19 13.4%, 0.27 15%, 0.34 16.1%, 0.54 18.35%, 0.66 20.6%, 0.72 22.4%, 0.77 24.6%, 0.81 27.3%, 0.85 30.4%, 0.88 35.1%, 0.92 40.6%, 0.94 47.2%, 0.96 55%, 0.98 64%, 0.99 74.4%, 1 86.4%, 1 100%) infinite;
    }
  </style>

  <div class="dynamic-gradient">
      <div class="relative isolate px-6 pt-2 lg:px-8">
        <div class="mx-auto max-w-7xl py-32 sm:py-48 lg:py-46">
          <div class="hidden sm:mb-8 sm:flex sm:justify-center">
            <div class="relative rounded-full px-2 py-1 text-sm text-gray-600 ring-1 ring-gray-900/10 hover:ring-gray-900/20">
              This is PMSi Internal Website only and is not available online. <a href="https://www.pmsi-cal.com" target="_blank" rel="noopener noreferrer" class="font-semibold text-emerald-600"><span class="absolute inset-0" aria-hidden="true"></span>Go to Official Website <span aria-hidden="true">&rarr;</span></a>
            </div>
          </div>
          <div class="text-center">
            <h1 class="text-5xl font-semibold tracking-tight text-gray-900 sm:text-7xl">Precision Measurement Specialists, <span class="text-red-500 italic">i</span>nc.</h1>
            <p class="mt-8 text-lg font-medium italic text-gray-500 sm:text-xl">'a metrology company'</p>
            <p class="text-lg font-medium italic text-gray-500 sm:text-xl">(since 1998)</p>
            <div class="mt-6 flex items-center justify-center gap-x-6">
              <a id="admin-btn" href="/admin" class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-md hover:bg-emerald-500 hover:text-gray-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500">ADMINISTRATION</a>
            </div>
            <p class="mt-4 text-center text-gray-700">Welcome to the PMSi internal website. Please click the 'Administration' button to proceed to the admin panel.</p>
          </div>
        </div>
      </div>
  </div>
@endsection