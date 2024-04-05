<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col">
                <!-- Data Points Table -->
                <h2>Data Points</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th>X</th>
                        <th>Y</th>
                        <th>Z</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(point, index) in dataPoints" :key="index">
                        <td><input type="number" class="form-control" v-model="point.x"/></td>
                        <td><input type="number" class="form-control" v-model="point.y"/></td>
                        <td><input type="number" class="form-control" v-model="point.z"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col">
                <!-- Centroids Table -->
                <h2>Centroids</h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th>X</th>
                        <th>Y</th>
                        <th>Z</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(centroid, index) in centroids" :key="index">
                        <td><input type="number" class="form-control" v-model="centroid.x"/></td>
                        <td><input type="number" class="form-control" v-model="centroid.y"/></td>
                        <td><input type="number" class="form-control" v-model="centroid.z"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col">
                <!-- Run K-Means Button -->
                <button @click="runKMeans" class="btn btn-primary">Run K-Means</button>
            </div>
        </div>

        <div class="row">
            <!-- Clusters -->
            <div class="col" v-for="(cluster, index) in clusters" :key="index">
                <div class="card my-3">
                    <div class="card-header">
                        <h2 class="card-title">Cluster {{ index + 1 }}</h2>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item" v-for="point in cluster" :key="point.id">{{ point }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
const MAX_ITERATIONS = 100;
const EPSILON = 0.001;
export default {
    data() {
        return {
            dataPoints: [
                {x: 1, y: 2, z: 2},
                {x: 2, y: 2, z: 2},
                {x: 4, y: 3, z: 3},
                {x: 5, y: 4, z: 4} // Add more if needed
            ],
            centroids: [
                {x: 1, y: 1, z: 1},
                {x: 2, y: 2, z: 3} // Add more centroids as needed
            ],
            k: 2, // Number of clusters
            clusters: []
        };
    },
    methods: {
        runKMeans() {
            // Step 1: Initialize centroids
            let centroids = this.centroids

            // Repeat until convergence or max iterations
            for (let i = 0; i < MAX_ITERATIONS; i++) {
                // Step 2: Assign data points to clusters
                this.clusters = this.assignToClusters(centroids);

                // Step 3: Update centroids
                let newCentroids = this.updateCentroids();

                // Check for convergence
                if (this.hasConverged(centroids, newCentroids)) {
                    break;
                }

                centroids = newCentroids;
            }
        },
        initializeCentroids() {
            // Randomly select K data points as initial centroids
            const centroids = [];
            const dataCopy = [...this.dataPoints];
            for (let i = 0; i < this.k; i++) {
                const randomIndex = Math.floor(Math.random() * dataCopy.length);
                centroids.push(dataCopy.splice(randomIndex, 1)[0]);
            }
            return centroids;
        },
        assignToClusters(centroids) {
            // Step 2: Assign data points to clusters
            const clusters = new Array(this.k).fill().map(() => []);
            for (const point of this.dataPoints) {
                const distances = centroids.map(centroid => this.euclideanDistance(point, centroid));
                const nearestCentroidIndex = distances.indexOf(Math.min(...distances));
                clusters[nearestCentroidIndex].push(point);
            }
            return clusters;
        },
        updateCentroids() {
            // Step 3: Update centroids
            const newCentroids = [];
            for (const cluster of this.clusters) {
                if (cluster.length === 0) {
                    // Handle empty clusters
                    newCentroids.push(this.randomDataPoint()); // Replace with proper handling
                } else {
                    const centroid = cluster.reduce((acc, curr) => ({
                        x: acc.x + curr.x,
                        y: acc.y + curr.y,
                        z: acc.z + curr.z
                    }), {x: 0, y: 0, z: 0});
                    newCentroids.push({
                        x: centroid.x / cluster.length,
                        y: centroid.y / cluster.length,
                        z: centroid.z / cluster.length
                    });
                }
            }
            return newCentroids;
        },
        euclideanDistance(point1, point2) {
            // Calculate Euclidean distance between two points
            return Math.sqrt(
                Math.pow(point1.x - point2.x, 2) +
                Math.pow(point1.y - point2.y, 2) +
                Math.pow(point1.z - point2.z, 2)
            );
        },
        hasConverged(centroids, newCentroids) {
            // Check for convergence
            for (let i = 0; i < centroids.length; i++) {
                if (this.euclideanDistance(centroids[i], newCentroids[i]) > EPSILON) {
                    return false;
                }
            }
            return true;
        },
        randomDataPoint() {
            // Return a random data point (for handling empty clusters)
            const randomIndex = Math.floor(Math.random() * this.dataPoints.length);
            return this.dataPoints[randomIndex];
        }
    }
};
</script>
