<?php

namespace App\Support;

class ValidationMessages
{
    public static function recipe(): array
    {
        return [
            'title.required' => 'Введіть назву рецепта.',
            'title.max' => 'Назва не може перевищувати 120 символів.',
            'description.required' => 'Введіть опис рецепта.',
            'description.max' => 'Опис не може перевищувати 2000 символів.',
            'category.required' => 'Оберіть категорію.',
            'category.in' => 'Обрана категорія недійсна.',
            'fats.required' => 'Вкажіть кількість жирів (г).',
            'fats.numeric' => 'Жири мають бути числом.',
            'fats.min' => 'Жири не можуть бути від\'ємними.',
            'fats.max' => 'Занадто велике значення жирів.',
            'carbs.required' => 'Вкажіть кількість вуглеводів (г).',
            'carbs.numeric' => 'Вуглеводи мають бути числом.',
            'carbs.min' => 'Вуглеводи не можуть бути від\'ємними.',
            'carbs.max' => 'Занадто велике значення вуглеводів.',
            'proteins.required' => 'Вкажіть кількість білків (г).',
            'proteins.numeric' => 'Білки мають бути числом.',
            'proteins.min' => 'Білки не можуть бути від\'ємними.',
            'proteins.max' => 'Занадто велике значення білків.',
            'photo.required' => 'Завантажте фото рецепта.',
            'photo.image' => 'Файл має бути зображенням.',
            'photo.max' => 'Фото не може перевищувати 4 МБ.',
            'ingredients_text.required' => 'Додайте хоча б один інгредієнт.',
            'steps.required' => 'Додайте хоча б один етап приготування.',
            'steps.min' => 'Додайте хоча б один етап приготування.',
            'steps.*.text.required' => 'Текст етапу обов\'язковий.',
            'steps.*.text.max' => 'Текст етапу не може перевищувати 2000 символів.',
            'steps.*.photo.image' => 'Фото етапу має бути зображенням.',
            'steps.*.photo.max' => 'Фото етапу не може перевищувати 4 МБ.',
        ];
    }

    public static function auth(): array
    {
        return [
            'email.required' => 'Введіть email.',
            'email.email' => 'Введіть коректний email.',
            'email.unique' => 'Цей email вже зареєстровано.',
            'password.required' => 'Введіть пароль.',
            'password.min' => 'Пароль має містити щонайменше 6 символів.',
            'password.confirmed' => 'Паролі не збігаються.',
            'name.required' => 'Введіть ім\'я.',
            'name.max' => 'Ім\'я не може перевищувати 255 символів.',
        ];
    }
}
