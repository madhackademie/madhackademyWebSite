#include "raylib.h"
#include <cstdio>

int main(void)
{
    int ageUser = 10;
    // create a windows for the app with size and title as param
    InitWindow(800, 450, "raylib [core] example - basic window");
    Color ageUserColorDisplay = WHITE;
    while (!WindowShouldClose())
    {
        BeginDrawing();
        // need to clear the background every frame see the tutorial on the hello world section
        ClearBackground(RAYWHITE);
        //1st methode
        if(ageUser <= 0)
        {
            ageUserColorDisplay = RED;
            DrawText("please give us a valide ageUser !!!", 190, 200, 20, ageUserColorDisplay);
        }
        else if(ageUser == 10)
        {
            ageUserColorDisplay = BLUE;
            DrawText("Ohh you are 10 years old you won the big lottery!!!!", 190, 200, 20, ageUserColorDisplay);
        }
        else
        {
            ageUserColorDisplay = GREEN;
            DrawText("ah nice you are alive !!!", 190, 200, 20, ageUserColorDisplay);
        }
        //2nd methode
        /*
        if(ageUser <= 0)
        {
            ageUserColorDisplay = RED;
            DrawText("please give us a valide ageUser !!!", 190, 200, 20, ageUserColorDisplay);
        }
        if(ageUser>=0 && ageUser!=10)
        {
            ageUserColorDisplay = GREEN;
            DrawText("ah nice you are alive !!!", 190, 200, 20, ageUserColorDisplay);
        }
        if(ageUser == 10)
        {
            ageUserColorDisplay = BLUE;
            DrawText("Ohh you are 10 years old you won the big lottery!!!!", 190, 200, 20, ageUserColorDisplay);
        }
        */
        EndDrawing();
    }
    CloseWindow();

    return 0;
}