#include "raylib.h"
#include <cstdio>
#include <vector>

using std::vector;

int main(void)
{
    // create a windows for the app with size and title as param
    InitWindow(800, 450, "GameDevReady-Container");

    //basic vector compteur code Exercice 1
    vector<int> carCollection;
    for (int i = 0; i < 5; i++)
    {
        carCollection.push_back(i + 1);
    }
    for (int i = 0; const auto& car : carCollection)
    {
        printf("car value : %d in position : %d\n", car, i);
        i++;
    }

    //multiplication the autocompletion fuck a bit but still working
    for (auto &&car : carCollection)
    {
        car *= 2;
    }
    for (const auto& car : carCollection)
    {
        printf("car value after X2 : %d\n",car);
    }

    //CHALLENGE Garage
    //struct
    struct Voiture
    {       
        float longueur_metre;
        int poids;
        float zero_to_100;
    };
    //add car
    vector<Voiture> GarageCollection;
    GarageCollection.push_back({2.5,2200,8});
    GarageCollection.push_back({2.8,2800,10});
    GarageCollection.push_back({2,1800,6});
    //new aliage bonus
    for (auto &&i : GarageCollection)
    {
        i.poids *=0.9;
    }
    //control debug
    for (int i = 0; const auto& voiture : GarageCollection)
    {
        i++;
        printf("poids de la voiture : %d egale : %d kilos\n",i, voiture.poids);
    }
    
    while (!WindowShouldClose())
    {
        BeginDrawing();
        // need to clear the background every frame see the tutorial on the hello world section
        ClearBackground(RAYWHITE);
        for (int i = 0;auto const& v : GarageCollection)
        {
            i++;
            DrawText(TextFormat("Poids de la voiture : %d, est de : %d kilos",i,v.poids),50,50 +i*50,25,BLUE);
            DrawText(TextFormat("Le zero a 100 de la voiture %d est atteind en %f s",i,v.zero_to_100),50,75 + i * 50,25,RED);
        }
        
        EndDrawing();
    }
    CloseWindow();

    return 0;
}