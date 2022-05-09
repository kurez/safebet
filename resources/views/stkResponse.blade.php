@extends('layouts.stkheader')

<div class="bg-white h-screen flex items-center justify-center">
	<div class=" text-center">
		<div class="text-7xl text-blue-300 leading-5">”</div>
		<div class="font-medium max-w-xl text-xl" style="float: left">
        
            @foreach ($data as $key => $value)
                {{ $value }}
                <br>
            @endforeach
   
		</div>
		<div class="mt-5">
			<span class="font-bold">Henrique Dubugras</span><span class="text-gray-500 font-medium"> — Co-Founder and CEO, Acme</span>
		</div>
	</div>
</div>


