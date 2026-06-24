#include "raylib.h"

int main(void)
{
    // create a windows for the app with size and title as param
    int age = 45; // or float age = 45.3
    bool x = true; // or int x = 5;
    bool test = true; // or int test = 12;
    float hightMeter = 1.85f; // not flaot
    bool isActive = true; // ok
    char firstLetter = 'b'; // ok
    int ageUser = 20;
    //array
    int note[5] = {6,8,9,1,4};
    //struct
    struct car
    {
        float sizeMeter;
        int WeightKilo;
        int MaxSpeed;
    };
    
    //pointer
    int userAge = 10;
    int* intPtr = nullptr;
    intPtr = &userAge;
    *intPtr += 25; // userAge is now 35

    
    InitWindow(800, 450, "raylib [core] example - basic window");
    ageUser += 10;
    while (!WindowShouldClose())
    {
        BeginDrawing();
        // need to clear the background every frame see the tutorial on the hello world section
        ClearBackground(RAYWHITE);
        DrawText(TextFormat("User age is : %u",ageUser), 190, 200, 20, BLUE);
        EndDrawing();
    }
    CloseWindow();

    return 0;
}