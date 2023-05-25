# Import Umum
import math
import re
from os import path
from datetime import datetime
import mysql.connector

# Data Fetcher
import pandas as pd

# Numerical
import numpy as np

# Accuracy
from sklearn.metrics import accuracy_score, mean_squared_error, mean_absolute_error

# Scaler
from sklearn.preprocessing import MinMaxScaler

# Model, LSTM
from keras.models import Sequential, load_model, model_from_json
from keras.layers import Dense, LSTM
from keras.preprocessing.sequence import TimeseriesGenerator

# Graph plot
import matplotlib.pyplot as plt

# Get Variable
import sys

# MAIN PROGRAM
def main():
  df = pd.read_csv('dataset_produksi_susu_test.csv', index_col='Tanggal',parse_dates=True)
  df.index.freq='MS'
  
  cek_awal = df.iloc[:12]
  cek = df.iloc[12:]
  
  # print(len(cek_awal[-12:]))
  # print(cek_awal[-12:])
  # print(cek)
  
  scaler = MinMaxScaler()
  
  scaler.fit(cek_awal)
  scaled_cek_awal = scaler.transform(cek_awal)
  scaled_cek = scaler.transform(cek)
  
  n_input = 12
  n_features = 1
  
  json_file = open('model_test.json', 'r')
  model_json = json_file.read()
  json_file.close()
  model = model_from_json(model_json)
  # load weights into new model
  model.load_weights("model_test.h5")
  
  model.compile(optimizer='adam', loss='mse')
  
  cek_pembuatans = []

  first_eval_batch = scaled_cek_awal[-n_input:]
  
  # print(first_eval_batch)
  # print(cek_awal[-n_input:])
  
  current_batch = first_eval_batch.reshape((1, n_input, n_features))
  # print(current_batch)

  for i in range(len(cek)):
      
      # get the prediction value for the first batch
      current_pred = model.predict(current_batch)[0]
      
      # append the prediction into the array
      cek_pembuatans.append(current_pred) 
      
      # use the prediction to update the batch and remove the first value
      current_batch = np.append(current_batch[:,1:,:],[[current_pred]],axis=1)
  
  # print(cek.head(),val.tail())
  
  true_cek_pembuatans = scaler.inverse_transform(cek_pembuatans)
  
  cek['Pembuatans'] = true_cek_pembuatans
  
  print(len(true_cek_pembuatans))
  print(true_cek_pembuatans)
  
  # RMSE
  rmse_cek = np.sqrt(mean_squared_error(cek['Pembuatan'],cek['Pembuatans']))
  print('Root mean square (RMSE) : ' + str(rmse_cek))
  

if __name__ == '__main__':
  main()